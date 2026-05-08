<?php

namespace App\Console\Commands;

use App\Services\CsvService;
use App\Models\Location;
use App\Models\User;
use App\Models\Client;
use App\Models\BillingRate;
use Illuminate\Console\Command;

class ExportDataToCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:export {type : locations|users|clients|billing_rates} {--output=storage/exports}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export data to CSV file';

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
        $type = $this->argument('type');
        $outputDir = $this->option('output');

        // Ensure output directory exists
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $this->info("Exporting {$type} data...");

        try {
            switch ($type) {
                case 'locations':
                    $this->exportLocations($outputDir);
                    break;
                case 'users':
                    $this->exportUsers($outputDir);
                    break;
                case 'clients':
                    $this->exportClients($outputDir);
                    break;
                case 'billing_rates':
                    $this->exportBillingRates($outputDir);
                    break;
                default:
                    $this->error("Invalid type: {$type}");
                    return 1;
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Export failed: " . $e->getMessage());
            return 1;
        }
    }

    protected function exportLocations($outputDir)
    {
        $this->info("Fetching locations...");
        $locations = Location::all(['id', 'region', 'district', 'ward', 'street'])->toArray();
        $count = count($locations);

        if ($count === 0) {
            $this->warn("No locations found!");
            return;
        }

        $filename = 'locations.csv';
        $filepath = "{$outputDir}/{$filename}";

        $result = $this->csvService->exportToCsv(
            $locations,
            $filename,
            ['id', 'region', 'district', 'ward', 'street']
        );

        file_put_contents($filepath, $result['content']);

        $this->info("✓ Successfully exported {$count} locations to {$filepath}");
    }

    protected function exportUsers($outputDir)
    {
        $this->info("Fetching users...");
        $users = User::all(['id', 'name', 'email', 'role', 'phone'])->toArray();
        $count = count($users);

        if ($count === 0) {
            $this->warn("No users found!");
            return;
        }

        $filename = 'users.csv';
        $filepath = "{$outputDir}/{$filename}";

        $result = $this->csvService->exportToCsv(
            $users,
            $filename,
            ['id', 'name', 'email', 'role', 'phone']
        );

        file_put_contents($filepath, $result['content']);

        $this->info("✓ Successfully exported {$count} users to {$filepath}");
    }

    protected function exportClients($outputDir)
    {
        $this->info("Fetching clients...");
        $clients = Client::all(['id', 'name', 'email', 'phone', 'region', 'district', 'ward', 'street'])->toArray();
        $count = count($clients);

        if ($count === 0) {
            $this->warn("No clients found!");
            return;
        }

        $filename = 'clients.csv';
        $filepath = "{$outputDir}/{$filename}";

        $result = $this->csvService->exportToCsv(
            $clients,
            $filename,
            ['id', 'name', 'email', 'phone', 'region', 'district', 'ward', 'street']
        );

        file_put_contents($filepath, $result['content']);

        $this->info("✓ Successfully exported {$count} clients to {$filepath}");
    }

    protected function exportBillingRates($outputDir)
    {
        $this->info("Fetching billing rates...");
        $rates = BillingRate::all(['id', 'region', 'district', 'ward', 'rate'])->toArray();
        $count = count($rates);

        if ($count === 0) {
            $this->warn("No billing rates found!");
            return;
        }

        $filename = 'billing_rates.csv';
        $filepath = "{$outputDir}/{$filename}";

        $result = $this->csvService->exportToCsv(
            $rates,
            $filename,
            ['id', 'region', 'district', 'ward', 'rate']
        );

        file_put_contents($filepath, $result['content']);

        $this->info("✓ Successfully exported {$count} billing rates to {$filepath}");
    }
}
