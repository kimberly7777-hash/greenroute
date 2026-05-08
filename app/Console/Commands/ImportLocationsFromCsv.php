<?php

namespace App\Console\Commands;

use App\Services\CsvService;
use App\Models\Location;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLocationsFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:import-locations {file} {--batch=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import locations from a CSV file';

    protected $csvService;

    public function __construct(CsvService $csvService)
    {
        parent::__construct();
        $this->csvService = $csvService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        $batchSize = $this->option('batch');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Starting import from: {$file}");

        try {
            // Read CSV
            $csvData = $this->csvService->readCsv($file);
            $data = $csvData['data'];
            $total = count($data);

            // Validate columns
            $required = ['region', 'district', 'ward', 'street'];
            $missing = array_diff($required, $csvData['headers']);

            if (!empty($missing)) {
                $this->error("Missing required columns: " . implode(', ', $missing));
                return 1;
            }

            $this->info("Total records to import: {$total}");

            // Create progress bar
            $progressBar = $this->output->createProgressBar($total);
            $progressBar->start();

            $inserted = 0;
            $skipped = 0;

            // Process in batches
            for ($i = 0; $i < $total; $i += $batchSize) {
                $batch = array_slice($data, $i, $batchSize);
                DB::beginTransaction();

                try {
                    foreach ($batch as $row) {
                        $row = $this->csvService->sanitize($row);

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
                        } else {
                            $skipped++;
                        }

                        $progressBar->advance();
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Batch error: " . $e->getMessage());
                    return 1;
                }
            }

            $progressBar->finish();
            $this->newLine(2);

            $this->info("✓ Import completed successfully!");
            $this->info("  Inserted: {$inserted}");
            $this->info("  Skipped (duplicates): {$skipped}");
            $this->info("  Total processed: {$total}");

            return 0;
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }
}
