<?php

namespace App\Console\Commands;

use App\Services\CsvService;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportUsersFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:import-users {file} {--batch=50}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users from a CSV file';

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

        $this->info("Starting user import from: {$file}");

        try {
            // Read CSV
            $csvData = $this->csvService->readCsv($file);
            $data = $csvData['data'];
            $total = count($data);

            // Validate columns
            $required = ['name', 'email', 'password', 'role'];
            $missing = array_diff($required, $csvData['headers']);

            if (!empty($missing)) {
                $this->error("Missing required columns: " . implode(', ', $missing));
                return 1;
            }

            $this->info("Total records to import: {$total}");

            // Validate data
            $validationRules = [
                'name' => 'required|min:3|max:255',
                'email' => 'required|email',
                'role' => 'required'
            ];

            $validation = $this->csvService->validateCsv($data, $validationRules);

            if (!$validation['valid']) {
                $this->error("CSV validation failed:");
                foreach ($validation['errors'] as $error) {
                    $this->error("  - {$error}");
                }
                return 1;
            }

            $progressBar = $this->output->createProgressBar(count($validation['data']));
            $progressBar->start();

            $inserted = 0;
            $skipped = 0;

            // Process in batches
            foreach ($validation['data'] as $row) {
                DB::beginTransaction();

                try {
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
                        $inserted++;
                    } else {
                        $skipped++;
                    }

                    DB::commit();
                    $progressBar->advance();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Error with user: {$row['email']} - {$e->getMessage()}");
                }
            }

            $progressBar->finish();
            $this->newLine(2);

            $this->info("✓ User import completed!");
            $this->info("  Inserted: {$inserted}");
            $this->info("  Skipped (duplicates): {$skipped}");

            return 0;
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }
}
