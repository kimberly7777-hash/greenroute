<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportLocationsFromUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locations:import-from-url {url : The URL to download locations JSON from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import locations from a JSON file URL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = $this->argument('url');
        
        $this->info("Downloading locations from: $url");
        
        try {
            // Download the JSON file
            $response = Http::timeout(120)->get($url);
            
            if (!$response->successful()) {
                $this->error("Failed to download file: " . $response->status());
                return 1;
            }
            
            $locations = $response->json();
            
            if (empty($locations)) {
                $this->error("No locations found in the JSON file");
                return 1;
            }
            
            $this->info("Found " . count($locations) . " locations. Starting import...");
            
            $bar = $this->output->createProgressBar(count($locations));
            $bar->start();
            
            $batch = [];
            $batchSize = 500;
            $imported = 0;
            
            foreach ($locations as $location) {
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
                    $bar->advance($batchSize);
                }
            }
            
            // Insert remaining
            if (!empty($batch)) {
                DB::table('tbl_locations')->insert($batch);
                $imported += count($batch);
                $bar->advance(count($batch));
            }
            
            $bar->finish();
            $this->newLine(2);
            $this->info("✅ Successfully imported $imported locations!");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
