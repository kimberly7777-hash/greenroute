<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if locations already exist
        $existingCount = Location::count();
        if ($existingCount > 0) {
            $this->command->info("Locations already seeded ({$existingCount} records). Skipping...");
            
            if ($this->command->confirm('Do you want to clear and re-import?', false)) {
                Location::truncate();
                $this->command->info('Existing locations cleared.');
            } else {
                return;
            }
        }

        $this->command->info('Importing location data from CSV file...');

        try {
            $csvPath = storage_path('app/tbl_locations.csv');
            
            if (!file_exists($csvPath)) {
                $this->command->error('CSV file not found at: ' . $csvPath);
                $this->command->info('Please ensure tbl_locations.csv exists in storage/app/');
                return;
            }

            $handle = fopen($csvPath, 'r');
            if (!$handle) {
                $this->command->error('Could not open CSV file');
                return;
            }

            // Read and skip header row
            $headers = fgetcsv($handle);
            $this->command->info('CSV Headers: ' . implode(', ', $headers));

            // Find column indices
            $regionIdx = array_search('region', $headers);
            $districtIdx = array_search('district', $headers);
            $wardIdx = array_search('ward', $headers);
            $streetIdx = array_search('street', $headers);

            if ($regionIdx === false || $districtIdx === false || $wardIdx === false) {
                $this->command->error('Required columns not found in CSV');
                fclose($handle);
                return;
            }

            DB::beginTransaction();

            $imported = 0;
            $skipped = 0;
            $batchSize = 500;
            $batch = [];
            
            $progressBar = $this->command->getOutput()->createProgressBar();
            $progressBar->start();

            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty rows
                if (empty($row) || count($row) < 3) {
                    $skipped++;
                    continue;
                }
                
                $region = isset($row[$regionIdx]) ? trim($row[$regionIdx]) : '';
                $district = isset($row[$districtIdx]) ? trim($row[$districtIdx]) : '';
                $ward = isset($row[$wardIdx]) ? trim($row[$wardIdx]) : '';
                $street = isset($row[$streetIdx]) ? trim($row[$streetIdx]) : null;
                
                // Skip if essential fields are empty
                if (empty($region) || empty($district) || empty($ward)) {
                    $skipped++;
                    continue;
                }
                
                // Add to batch
                $batch[] = [
                    'region' => $region,
                    'district' => $district,
                    'ward' => $ward,
                    'street' => $street,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Insert batch when it reaches the batch size
                if (count($batch) >= $batchSize) {
                    Location::insert($batch);
                    $imported += count($batch);
                    $progressBar->advance(count($batch));
                    $batch = [];
                }
            }
            
            // Insert remaining records
            if (!empty($batch)) {
                Location::insert($batch);
                $imported += count($batch);
                $progressBar->advance(count($batch));
            }
            
            DB::commit();
            fclose($handle);
            
            $progressBar->finish();
            $this->command->newLine(2);
            
            $this->command->info("✅ Import completed successfully!");
            $this->command->info("   📊 Total imported: " . number_format($imported));
            $this->command->info("   ⏭️  Skipped: " . number_format($skipped));
            
            // Show statistics
            $this->command->newLine();
            $this->command->info("📈 Database Statistics:");
            $this->command->info("   Regions: " . Location::distinct('region')->count('region'));
            $this->command->info("   Districts: " . Location::distinct('district')->count('district'));
            $this->command->info("   Wards: " . Location::distinct('ward')->count('ward'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($handle)) {
                fclose($handle);
            }
            $this->command->error('Error importing locations: ' . $e->getMessage());
            $this->command->error('   File: ' . $e->getFile());
            $this->command->error('   Line: ' . $e->getLine());
        }
    }
}
