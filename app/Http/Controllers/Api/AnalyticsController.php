<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contractor;
use App\Models\Invoice;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    /**
     * Get comprehensive dashboard analytics for a contractor
     */
    public function contractorDashboard(Request $request)
    {
        $request->validate([
            'contractor_registration_number' => 'required|string|exists:contractors,registration_number'
        ]);

        try {
            $contractor = Contractor::where('registration_number', $request->contractor_registration_number)
                ->firstOrFail();

            $contractorId = $contractor->user_id;

            // Cache for 5 minutes
            $cacheKey = "analytics:contractor:{$contractor->registration_number}";
            
            $analytics = Cache::remember($cacheKey, 300, function () use ($contractorId) {
                return [
                    'clients' => $this->getClientAnalytics($contractorId),
                    'invoices' => $this->getInvoiceAnalytics($contractorId),
                    'locations' => $this->getLocationAnalytics($contractorId),
                    'revenue' => $this->getRevenueAnalytics($contractorId),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client analytics
     */
    private function getClientAnalytics($contractorId)
    {
        $totalClients = Client::forContractor($contractorId)->count();
        $activeClients = Client::forContractor($contractorId)->active()->count();
        $inactiveClients = $totalClients - $activeClients;

        $clientsByLocation = Client::forContractor($contractorId)
            ->active()
            ->whereNotNull('region')
            ->select('region', DB::raw('count(*) as count'))
            ->groupBy('region')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'region' => $item->region,
                    'client_count' => $item->count
                ];
            });

        return [
            'total' => $totalClients,
            'active' => $activeClients,
            'inactive' => $inactiveClients,
            'by_region' => $clientsByLocation,
            'with_location' => Client::forContractor($contractorId)
                ->whereNotNull('region')
                ->whereNotNull('district')
                ->whereNotNull('ward')
                ->count(),
        ];
    }

    /**
     * Get invoice analytics
     */
    private function getInvoiceAnalytics($contractorId)
    {
        $totalInvoices = Invoice::forContractor($contractorId)->count();
        
        $invoicesByStatus = Invoice::forContractor($contractorId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $recentInvoices = Invoice::forContractor($contractorId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($invoice) {
                return [
                    'invoice_number' => $invoice->invoice_number,
                    'client_name' => $invoice->client?->name,
                    'total_amount' => (float) $invoice->total_amount,
                    'status' => $invoice->status,
                    'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
                ];
            });

        return [
            'total' => $totalInvoices,
            'draft' => $invoicesByStatus['draft'] ?? 0,
            'sent' => $invoicesByStatus['sent'] ?? 0,
            'paid' => $invoicesByStatus['paid'] ?? 0,
            'overdue' => $invoicesByStatus['overdue'] ?? 0,
            'cancelled' => $invoicesByStatus['cancelled'] ?? 0,
            'recent' => $recentInvoices,
        ];
    }

    /**
     * Get location distribution analytics
     */
    private function getLocationAnalytics($contractorId)
    {
        $locationDistribution = Client::forContractor($contractorId)
            ->active()
            ->whereNotNull('region')
            ->whereNotNull('district')
            ->whereNotNull('ward')
            ->select('region', 'district', 'ward', DB::raw('count(*) as client_count'))
            ->groupBy('region', 'district', 'ward')
            ->orderBy('client_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'location' => "{$item->region} > {$item->district} > {$item->ward}",
                    'region' => $item->region,
                    'district' => $item->district,
                    'ward' => $item->ward,
                    'client_count' => $item->client_count
                ];
            });

        return [
            'top_locations' => $locationDistribution,
            'unique_regions' => Client::forContractor($contractorId)
                ->distinct('region')
                ->whereNotNull('region')
                ->count(),
            'unique_districts' => Client::forContractor($contractorId)
                ->distinct('district')
                ->whereNotNull('district')
                ->count(),
            'unique_wards' => Client::forContractor($contractorId)
                ->distinct('ward')
                ->whereNotNull('ward')
                ->count(),
        ];
    }

    /**
     * Get revenue analytics
     */
    private function getRevenueAnalytics($contractorId)
    {
        $totalRevenue = Invoice::forContractor($contractorId)
            ->sum('total_amount');

        $paidRevenue = Invoice::forContractor($contractorId)
            ->where('status', 'paid')
            ->sum('total_amount');

        $pendingRevenue = Invoice::forContractor($contractorId)
            ->whereIn('status', ['draft', 'sent'])
            ->sum('total_amount');

        $overdueRevenue = Invoice::forContractor($contractorId)
            ->where('status', 'overdue')
            ->sum('total_amount');

        // Monthly revenue (last 6 months)
        $monthlyRevenue = Invoice::forContractor($contractorId)
            ->where('invoice_date', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(invoice_date, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as paid'),
                DB::raw('COUNT(*) as invoice_count')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'total_invoiced' => (float) $item->total,
                    'total_paid' => (float) $item->paid,
                    'invoice_count' => $item->invoice_count,
                ];
            });

        return [
            'total_invoiced' => (float) $totalRevenue,
            'total_paid' => (float) $paidRevenue,
            'pending' => (float) $pendingRevenue,
            'overdue' => (float) $overdueRevenue,
            'collection_rate' => $totalRevenue > 0 ? round(($paidRevenue / $totalRevenue) * 100, 2) : 0,
            'monthly' => $monthlyRevenue,
        ];
    }

    /**
     * Get location-based revenue analytics
     */
    public function locationRevenue(Request $request)
    {
        $request->validate([
            'contractor_registration_number' => 'required|string|exists:contractors,registration_number',
            'region' => 'nullable|string',
            'district' => 'nullable|string',
            'ward' => 'nullable|string',
        ]);

        try {
            $contractor = Contractor::where('registration_number', $request->contractor_registration_number)
                ->firstOrFail();

            $contractorId = $contractor->user_id;

            // Get clients in specified location
            $clientQuery = Client::forContractor($contractorId);

            if ($request->region) {
                $clientQuery->where('region', $request->region);
            }
            if ($request->district) {
                $clientQuery->where('district', $request->district);
            }
            if ($request->ward) {
                $clientQuery->where('ward', $request->ward);
            }

            $clientIds = $clientQuery->pluck('id');

            // Get invoice statistics for these clients
            $totalRevenue = Invoice::forContractor($contractorId)
                ->whereIn('client_id', $clientIds)
                ->sum('total_amount');

            $paidRevenue = Invoice::forContractor($contractorId)
                ->whereIn('client_id', $clientIds)
                ->where('status', 'paid')
                ->sum('total_amount');

            $invoiceCount = Invoice::forContractor($contractorId)
                ->whereIn('client_id', $clientIds)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'location' => implode(' > ', array_filter([
                        $request->region,
                        $request->district,
                        $request->ward
                    ])) ?: 'All Locations',
                    'client_count' => $clientIds->count(),
                    'invoice_count' => $invoiceCount,
                    'total_revenue' => (float) $totalRevenue,
                    'paid_revenue' => (float) $paidRevenue,
                    'pending_revenue' => (float) ($totalRevenue - $paidRevenue),
                    'average_per_client' => $clientIds->count() > 0 
                        ? round($totalRevenue / $clientIds->count(), 2) 
                        : 0,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve location revenue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear analytics cache
     */
    public function clearCache(Request $request)
    {
        $request->validate([
            'contractor_registration_number' => 'required|string|exists:contractors,registration_number'
        ]);

        $contractor = Contractor::where('registration_number', $request->contractor_registration_number)
            ->firstOrFail();

        $cacheKey = "analytics:contractor:{$contractor->registration_number}";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Analytics cache cleared successfully'
        ]);
    }
}
