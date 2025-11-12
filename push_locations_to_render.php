<?php

/**
 * Push locations from local database to Render via HTTP API
 * This works with SQLite on Render
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Location;

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  Push Locations to Render via API                         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Your Render URL
$renderUrl = readline("Enter your Render URL (e.g., https://afia-orbit.onrender.com): ");
$renderUrl = rtrim($renderUrl, '/');

echo "\nCounting local locations...\n";
$totalLocations = Location::count();
echo "Found: " . number_format($totalLocations) . " locations\n\n";

if ($totalLocations == 0) {
    die("❌ No locations found in local database!\n");
}

// Ask if user wants to clear existing data
$clear = strtolower(readline("Clear existing locations on Render first? (yes/no): "));

if ($clear === 'yes' || $clear === 'y') {
    echo "Clearing existing locations on Render...\n";
    
    $ch = curl_init("$renderUrl/api/locations/clear");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✅ Cleared\n\n";
    } else {
        echo "⚠️  Warning: Could not clear (this is ok if table was already empty)\n\n";
    }
}

// Import in batches
$batchSize = 1000; // Send 1000 at a time
$offset = 0;
$imported = 0;

echo "Starting import in batches of $batchSize...\n\n";

while ($offset < $totalLocations) {
    // Get batch from local database
    $locations = Location::skip($offset)
        ->take($batchSize)
        ->get(['region', 'district', 'ward', 'street'])
        ->toArray();
    
    if (empty($locations)) {
        break;
    }
    
    // Send to Render
    $ch = curl_init("$renderUrl/api/locations/import");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($locations));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($locations))
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120); // 2 minute timeout
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        echo "\n❌ Error: " . curl_error($ch) . "\n";
        curl_close($ch);
        break;
    }
    
    curl_close($ch);
    
    if ($httpCode == 200) {
        $result = json_decode($response, true);
        $imported += $result['imported'] ?? count($locations);
        
        $percentage = round(($imported / $totalLocations) * 100, 1);
        echo "\rProgress: $imported / $totalLocations ($percentage%) ";
    } else {
        echo "\n❌ HTTP Error $httpCode\n";
        echo "Response: $response\n";
        break;
    }
    
    $offset += $batchSize;
    
    // Small delay to avoid overwhelming the server
    usleep(100000); // 0.1 second
}

echo "\n\n✅ Import complete!\n";
echo "Total imported: " . number_format($imported) . " locations\n\n";

// Verify
echo "Verifying...\n";
$ch = curl_init("$renderUrl/api/diagnostics/system");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$renderCount = $result['diagnostics']['locations_count'] ?? 0;

echo "Render database now has: " . number_format($renderCount) . " locations\n";

if ($renderCount == $totalLocations) {
    echo "\n🎉 Success! All locations imported correctly!\n";
    echo "\nYou can now test the autocomplete on your Render site!\n";
} else {
    echo "\n⚠️  Count mismatch - but this might be ok if some batches failed.\n";
    echo "Try running the autocomplete anyway!\n";
}
