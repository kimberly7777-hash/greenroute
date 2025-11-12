<?php

// Export locations to JSON format for easy import
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Location;

echo "Exporting locations to JSON...\n";

$locations = Location::all(['region', 'district', 'ward', 'street']);

$jsonFile = 'locations_export.json';
file_put_contents($jsonFile, json_encode($locations, JSON_PRETTY_PRINT));

$count = $locations->count();
$size = filesize($jsonFile);
$sizeMB = round($size / 1024 / 1024, 2);

echo "✅ Exported $count locations to $jsonFile\n";
echo "📦 File size: {$sizeMB}MB\n";
echo "\nYou can now:\n";
echo "1. Upload this file to a file hosting service (Dropbox, Google Drive, etc.)\n";
echo "2. Get a direct download link\n";
echo "3. Use the import command with that URL\n";
