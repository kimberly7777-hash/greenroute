<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This will import locations from the CSV file that was previously imported
     */
    public function run(): void
    {
        $csvFile = storage_path('app/tbl_locations.csv');
        $alternateCsvFile = storage_path('app/locations.csv');

        if (!file_exists($csvFile) && file_exists($alternateCsvFile)) {
            $csvFile = $alternateCsvFile;
        }

        if (!file_exists($csvFile)) {
            $this->command->error('CSV file not found in storage/app/');
            $this->command->info('Please upload tbl_locations.csv or locations.csv to storage/app/ directory');
            return;
        }
        
        $this->command->info('Starting location import from CSV...');
        
        $handle = fopen($csvFile, 'r');
        $header = fgetcsv($handle); // Skip header row
        
        $batch = [];
        $count = 0;
        $batchSize = 500;
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 4) {
                $batch[] = [
                    'region' => $row[0],
                    'district' => $row[1],
                    'ward' => $row[2],
                    'street' => $row[3] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $count++;
                
                // Insert in batches for performance
                if (count($batch) >= $batchSize) {
                    DB::table('tbl_locations')->insert($batch);
                    $this->command->info("Imported $count locations...");
                    $batch = [];
                }
            }
        }
        
        // Insert remaining records
        if (!empty($batch)) {
            DB::table('tbl_locations')->insert($batch);
        }
        
        fclose($handle);
        
        $this->command->info("✅ Successfully imported $count locations!");
    }
}
