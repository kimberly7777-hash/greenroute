<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Contractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LocationInvoiceController extends Controller
{
    /**
     * Get all clients at a specific site location for a contractor
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClientsByLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contractor_registration_number' => 'required|string|exists:contractors,registration_number',
            'region' => 'required|string',
            'district' => 'required|string',
            'ward' => 'required|string',
            'street' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get contractor
            $contractor = Contractor::where('registration_number', $request->contractor_registration_number)
                ->firstOrFail();

            // Get all clients at this location for this contractor
            $clients = Client::forContractor($contractor->user_id)
                ->byLocation(
                    $request->region,
                    $request->district,
                    $request->ward,
                    $request->street
                )
                ->active()
                ->select([
                    'id',
                    'registration_number',
                    'name',
                    'email',
                    'phone',
                    'address',
                    'region',
                    'district',
                    'ward',
                    'street',
                    'status'
                ])
                ->orderBy('name')
                ->get();

            $siteLocation = implode(' > ', array_filter([
                $request->region,
                $request->district,
                $request->ward,
                $request->street
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Clients retrieved successfully',
                'data' => [
                    'site_location' => $siteLocation,
                    'region' => $request->region,
                    'district' => $request->district,
                    'ward' => $request->ward,
                    'street' => $request->street,
                    'clients' => $clients,
                    'total_clients' => $clients->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve clients',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create invoices for multiple clients at a site location (BULK INVOICE CREATION)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBulkInvoices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contractor_registration_number' => 'required|string|exists:contractors,registration_number',
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'required|exists:clients,id',
            'site_location' => 'required|array',
            'site_location.region' => 'required|string',
            'site_location.district' => 'required|string',
            'site_location.ward' => 'required|string',
            'site_location.street' => 'nullable|string',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'service_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Get contractor
            $contractor = Contractor::where('registration_number', $request->contractor_registration_number)
                ->firstOrFail();

            $contractorId = $contractor->user_id;
            $clientIds = $request->client_ids;

            // Verify all clients belong to this contractor and are at the specified location
            $clients = Client::whereIn('id', $clientIds)
                ->where('contractor_id', $contractorId)
                ->byLocation(
                    $request->site_location['region'],
                    $request->site_location['district'],
                    $request->site_location['ward'],
                    $request->site_location['street'] ?? null
                )
                ->get();

            if ($clients->count() !== count($clientIds)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Some clients do not belong to this contractor or are not at the specified location'
                ], 400);
            }

            $createdInvoices = [];
            $failedInvoices = [];

            // Create invoice for each selected client
            foreach ($clients as $client) {
                try {
                    $invoice = new Invoice();
                    $invoice->contractor_id = $contractorId;
                    $invoice->client_id = $client->id;
                    $invoice->contractor_registration_number = $contractor->registration_number;
                    $invoice->client_registration_number = $client->registration_number;
                    $invoice->invoice_date = $request->invoice_date;
                    $invoice->due_date = $request->due_date;
                    $invoice->service_type = $request->service_type;
                    $invoice->description = $request->description;
                    $invoice->subtotal = $request->subtotal;
                    $invoice->tax_rate = $request->tax_rate;
                    $invoice->notes = $request->notes;
                    $invoice->status = 'draft';
                    $invoice->invoice_number = $invoice->generateInvoiceNumber();
                    
                    // Calculate totals
                    $invoice->calculateTotals();

                    $createdInvoices[] = [
                        'client_id' => $client->id,
                        'client_name' => $client->name,
                        'client_registration_number' => $client->registration_number,
                        'invoice_number' => $invoice->invoice_number,
                        'total_amount' => $invoice->total_amount
                    ];

                } catch (\Exception $e) {
                    $failedInvoices[] = [
                        'client_id' => $client->id,
                        'client_name' => $client->name,
                        'error' => $e->getMessage()
                    ];
                }
            }

            if (!empty($failedInvoices)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Some invoices failed to create',
                    'data' => [
                        'created' => $createdInvoices,
                        'failed' => $failedInvoices
                    ]
                ], 500);
            }

            DB::commit();

            $siteLocationString = implode(' > ', array_filter([
                $request->site_location['region'],
                $request->site_location['district'],
                $request->site_location['ward'],
                $request->site_location['street'] ?? null
            ]));

            return response()->json([
                'success' => true,
                'message' => "Successfully created {$clients->count()} invoices for site location: {$siteLocationString}",
                'data' => [
                    'site_location' => $siteLocationString,
                    'invoices_created' => count($createdInvoices),
                    'total_amount' => array_sum(array_column($createdInvoices, 'total_amount')),
                    'invoices' => $createdInvoices
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bulk invoices',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get location statistics for a contractor's clients
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocationStatistics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contractor_registration_number' => 'required|string|exists:contractors,registration_number'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $contractor = Contractor::where('registration_number', $request->contractor_registration_number)
                ->firstOrFail();

            // Get location breakdown
            $locationStats = Client::forContractor($contractor->user_id)
                ->active()
                ->selectRaw('region, district, ward, street, COUNT(*) as client_count')
                ->whereNotNull('region')
                ->whereNotNull('district')
                ->whereNotNull('ward')
                ->groupBy('region', 'district', 'ward', 'street')
                ->orderBy('client_count', 'desc')
                ->get()
                ->map(function ($stat) {
                    return [
                        'site_location' => implode(' > ', array_filter([
                            $stat->region,
                            $stat->district,
                            $stat->ward,
                            $stat->street
                        ])),
                        'region' => $stat->region,
                        'district' => $stat->district,
                        'ward' => $stat->ward,
                        'street' => $stat->street,
                        'client_count' => $stat->client_count
                    ];
                });

            $totalClients = Client::forContractor($contractor->user_id)->active()->count();
            $clientsWithLocation = Client::forContractor($contractor->user_id)
                ->active()
                ->whereNotNull('region')
                ->whereNotNull('district')
                ->whereNotNull('ward')
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Location statistics retrieved successfully',
                'data' => [
                    'total_clients' => $totalClients,
                    'clients_with_location' => $clientsWithLocation,
                    'clients_without_location' => $totalClients - $clientsWithLocation,
                    'unique_locations' => $locationStats->count(),
                    'locations' => $locationStats
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve location statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
