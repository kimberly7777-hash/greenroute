<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationImportController extends Controller
{
    /**
     * Import locations from JSON data
     * This endpoint accepts JSON array of locations and imports them in batches
     */
    public function importFromJson(Request $request)
    {
        try {
            $locations = $request->json()->all();
            
            if (empty($locations)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No locations provided'
                ], 400);
            }
            
            $batchSize = 500;
            $batch = [];
            $imported = 0;
            
            foreach ($locations as $location) {
                if (!isset($location['region']) || !isset($location['district']) || !isset($location['ward'])) {
                    continue;
                }
                
                $batch[] = [
                    'region' => $location['region'],
                    'district' => $location['district'],
                    'ward' => $location['ward'],
                    'street' => $location['street'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if (count($batch) >= $batchSize) {
                    DB::table('tbl_locations')->insert($batch);
                    $imported += count($batch);
                    $batch = [];
                }
            }
            
            // Insert remaining
            if (!empty($batch)) {
                DB::table('tbl_locations')->insert($batch);
                $imported += count($batch);
            }
            
            return response()->json([
                'success' => true,
                'imported' => $imported,
                'message' => "Successfully imported $imported locations"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Location import error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Import failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear all locations (use with caution)
     */
    public function clearAll(Request $request)
    {
        try {
            $count = DB::table('tbl_locations')->count();
            DB::table('tbl_locations')->truncate();
            
            return response()->json([
                'success' => true,
                'message' => "Cleared $count locations"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
