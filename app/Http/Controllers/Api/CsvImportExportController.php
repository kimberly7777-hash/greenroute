<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CsvService;
use App\Models\Location;
use App\Models\User;
use App\Models\Client;
use App\Models\Contractor;
use App\Models\BillingRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CsvImportExportController extends Controller
{
    protected $csvService;

    public function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    /**
     * Preview CSV file before import
     */
    public function preview(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:10240',
                'type' => 'required|in:locations,users,clients,contractors,billing_rates,routes'
            ]);

            $file = $request->file('file');
            $type = $request->input('type');

            // Store temporarily
            $tempPath = $file->store('temp_csv');
            $fullPath = storage_path('app/' . $tempPath);

            // Get sample
            $sample = $this->csvService->getSample($fullPath, 5);

            // Clean up
            @unlink($fullPath);

            return response()->json([
                'success' => true,
                'message' => 'CSV preview loaded successfully',
                'type' => $type,
                'headers' => $sample['headers'],
                'sample' => $sample['sample'],
                'totalRows' => $sample['totalRows'],
                'sampleSize' => $sample['sampleSize']
            ]);
        } catch (\Exception $e) {
            Log::error('CSV Preview Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error previewing CSV: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Import Locations from CSV
     */
    public function importLocations(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:20480'
            ]);

            $file = $request->file('file');
            $tempPath = $file->store('temp_csv');
            $fullPath = storage_path('app/' . $tempPath);

            // Read CSV
            $csvData = $this->csvService->readCsv($fullPath);

            // Validate required columns
            $requiredColumns = ['region', 'district', 'ward', 'street'];
            $missingColumns = array_diff($requiredColumns, $csvData['headers']);
            if (!empty($missingColumns)) {
                throw new \Exception('Missing required columns: ' . implode(', ', $missingColumns));
            }

            // Process in batches
            $result = $this->csvService->processCsvInBatches($fullPath, 100, function ($batch) {
                $inserted = 0;
                DB::beginTransaction();

                try {
                    foreach ($batch as $row) {
                        $row = $this->csvService->sanitize($row);

                        // Check if location already exists
                        $exists = Location::where('region', $row['region'])
                            ->where('district', $row['district'])
                            ->where('ward', $row['ward'])
                            ->where('street', $row['street'])
                            ->exists();

                        if (!$exists) {
                            Location::create([
                                'region' => $row['region'],
                                'district' => $row['district'],
                                'ward' => $row['ward'],
                                'street' => $row['street']
                            ]);
                            $inserted++;
                        }
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Batch insert error: ' . $e->getMessage());
                }

                return ['inserted' => $inserted];
            });

            // Clean up
            @unlink($fullPath);

            $totalInserted = array_sum(array_column($result['results'], 'inserted'));

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$totalInserted} locations",
                'imported' => $totalInserted,
                'total' => $result['totalRows'],
                'batches' => $result['batches']
            ]);
        } catch (\Exception $e) {
            Log::error('Location Import Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error importing locations: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Import Users/Staff from CSV
     */
    public function importUsers(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:10240'
            ]);

            $file = $request->file('file');
            $tempPath = $file->store('temp_csv');
            $fullPath = storage_path('app/' . $tempPath);

            // Read and validate CSV
            $csvData = $this->csvService->readCsv($fullPath);
            $requiredColumns = ['name', 'email', 'password', 'role'];
            $missingColumns = array_diff($requiredColumns, $csvData['headers']);

            if (!empty($missingColumns)) {
                throw new \Exception('Missing required columns: ' . implode(', ', $missingColumns));
            }

            // Validate data
            $validationRules = [
                'name' => 'required|min:3|max:255',
                'email' => 'required|email',
                'role' => 'required'
            ];

            $validation = $this->csvService->validateCsv($csvData['data'], $validationRules);

            if (!$validation['valid']) {
                @unlink($fullPath);
                return response()->json([
                    'success' => false,
                    'message' => 'CSV validation failed',
                    'errors' => $validation['errors']
                ], 422);
            }

            // Import users
            $imported = 0;
            DB::beginTransaction();

            try {
                foreach ($validation['data'] as $row) {
                    $row = $this->csvService->sanitize($row);

                    $exists = User::where('email', $row['email'])->exists();
                    if (!$exists) {
                        User::create([
                            'name' => $row['name'],
                            'email' => $row['email'],
                            'password' => bcrypt($row['password']),
                            'role' => $row['role'],
                            'phone' => $row['phone'] ?? null
                        ]);
                        $imported++;
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            @unlink($fullPath);

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$imported} users",
                'imported' => $imported,
                'total' => count($validation['data'])
            ]);
        } catch (\Exception $e) {
            Log::error('User Import Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error importing users: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Import Clients from CSV
     */
    public function importClients(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:10240'
            ]);

            $file = $request->file('file');
            $tempPath = $file->store('temp_csv');
            $fullPath = storage_path('app/' . $tempPath);

            $csvData = $this->csvService->readCsv($fullPath);
            $requiredColumns = ['name', 'email', 'phone'];
            $missingColumns = array_diff($requiredColumns, $csvData['headers']);

            if (!empty($missingColumns)) {
                throw new \Exception('Missing required columns: ' . implode(', ', $missingColumns));
            }

            // Validate
            $validationRules = [
                'name' => 'required|min:3',
                'email' => 'required|email',
                'phone' => 'required|phone'
            ];

            $validation = $this->csvService->validateCsv($csvData['data'], $validationRules);

            if (!$validation['valid']) {
                @unlink($fullPath);
                return response()->json([
                    'success' => false,
                    'message' => 'CSV validation failed',
                    'errors' => $validation['errors']
                ], 422);
            }

            // Import clients
            $imported = 0;
            DB::beginTransaction();

            try {
                foreach ($validation['data'] as $row) {
                    $row = $this->csvService->sanitize($row);

                    $exists = Client::where('email', $row['email'])->exists();
                    if (!$exists) {
                        Client::create([
                            'name' => $row['name'],
                            'email' => $row['email'],
                            'phone' => $row['phone'],
                            'region' => $row['region'] ?? null,
                            'district' => $row['district'] ?? null,
                            'ward' => $row['ward'] ?? null,
                            'street' => $row['street'] ?? null
                        ]);
                        $imported++;
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            @unlink($fullPath);

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$imported} clients",
                'imported' => $imported,
                'total' => count($validation['data'])
            ]);
        } catch (\Exception $e) {
            Log::error('Client Import Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error importing clients: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Import Billing Rates from CSV
     */
    public function importBillingRates(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:10240'
            ]);

            $file = $request->file('file');
            $tempPath = $file->store('temp_csv');
            $fullPath = storage_path('app/' . $tempPath);

            $csvData = $this->csvService->readCsv($fullPath);
            $requiredColumns = ['region', 'rate'];
            $missingColumns = array_diff($requiredColumns, $csvData['headers']);

            if (!empty($missingColumns)) {
                throw new \Exception('Missing required columns: ' . implode(', ', $missingColumns));
            }

            // Validate
            $validationRules = [
                'region' => 'required',
                'rate' => 'required|numeric'
            ];

            $validation = $this->csvService->validateCsv($csvData['data'], $validationRules);

            if (!$validation['valid']) {
                @unlink($fullPath);
                return response()->json([
                    'success' => false,
                    'message' => 'CSV validation failed',
                    'errors' => $validation['errors']
                ], 422);
            }

            // Import rates
            $imported = 0;
            DB::beginTransaction();

            try {
                foreach ($validation['data'] as $row) {
                    $row = $this->csvService->sanitize($row);

                    BillingRate::updateOrCreate(
                        [
                            'region' => $row['region'],
                            'district' => $row['district'] ?? null,
                            'ward' => $row['ward'] ?? null
                        ],
                        [
                            'rate' => $row['rate']
                        ]
                    );
                    $imported++;
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            @unlink($fullPath);

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$imported} billing rates",
                'imported' => $imported,
                'total' => count($validation['data'])
            ]);
        } catch (\Exception $e) {
            Log::error('Billing Rate Import Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error importing billing rates: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Export Locations to CSV
     */
    public function exportLocations()
    {
        try {
            $locations = Location::all(['id', 'region', 'district', 'ward', 'street'])->toArray();

            if (empty($locations)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No locations to export'
                ], 404);
            }

            $result = $this->csvService->exportToCsv(
                $locations,
                'locations.csv',
                ['id', 'region', 'district', 'ward', 'street']
            );

            return response()->streamDownload(
                function () use ($result) {
                    echo $result['content'];
                },
                'locations.csv',
                ['Content-Type' => 'text/csv']
            );
        } catch (\Exception $e) {
            Log::error('Location Export Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting locations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Users to CSV
     */
    public function exportUsers()
    {
        try {
            $users = User::all(['id', 'name', 'email', 'role', 'phone'])->toArray();

            if (empty($users)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users to export'
                ], 404);
            }

            $result = $this->csvService->exportToCsv(
                $users,
                'users.csv',
                ['id', 'name', 'email', 'role', 'phone']
            );

            return response()->streamDownload(
                function () use ($result) {
                    echo $result['content'];
                },
                'users.csv',
                ['Content-Type' => 'text/csv']
            );
        } catch (\Exception $e) {
            Log::error('User Export Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Clients to CSV
     */
    public function exportClients()
    {
        try {
            $clients = Client::all(['id', 'name', 'email', 'phone', 'region', 'district', 'ward', 'street'])->toArray();

            if (empty($clients)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No clients to export'
                ], 404);
            }

            $result = $this->csvService->exportToCsv(
                $clients,
                'clients.csv',
                ['id', 'name', 'email', 'phone', 'region', 'district', 'ward', 'street']
            );

            return response()->streamDownload(
                function () use ($result) {
                    echo $result['content'];
                },
                'clients.csv',
                ['Content-Type' => 'text/csv']
            );
        } catch (\Exception $e) {
            Log::error('Client Export Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting clients: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Billing Rates to CSV
     */
    public function exportBillingRates()
    {
        try {
            $rates = BillingRate::all(['id', 'region', 'district', 'ward', 'rate'])->toArray();

            if (empty($rates)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No billing rates to export'
                ], 404);
            }

            $result = $this->csvService->exportToCsv(
                $rates,
                'billing_rates.csv',
                ['id', 'region', 'district', 'ward', 'rate']
            );

            return response()->streamDownload(
                function () use ($result) {
                    echo $result['content'];
                },
                'billing_rates.csv',
                ['Content-Type' => 'text/csv']
            );
        } catch (\Exception $e) {
            Log::error('Billing Rate Export Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting billing rates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get CSV import statistics
     */
    public function getStats()
    {
        try {
            return response()->json([
                'success' => true,
                'stats' => [
                    'locations' => Location::count(),
                    'users' => User::count(),
                    'clients' => Client::count(),
                    'billing_rates' => BillingRate::count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics'
            ], 500);
        }
    }
}
