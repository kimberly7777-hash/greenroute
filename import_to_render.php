<?php

/**
 * Import locations from local database to Render database
 * 
 * This script reads from your local database and imports directly to Render
 */

require __DIR__.'/vendor/autoload.php';

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  Import Locations from Local to Render Database           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Get Render database credentials
echo "Enter your Render database credentials:\n\n";

$renderHost = readline("Database Host (e.g., oregon-postgres.render.com): ");
$renderPort = readline("Port (default 5432): ") ?: "5432";
$renderDatabase = readline("Database Name: ");
$renderUsername = readline("Username: ");
$renderPassword = readline("Password: ");

echo "\nConnecting to databases...\n";

// Connect to local database
try {
    $localPdo = new PDO(
        'mysql:host=127.0.0.1;dbname=' . env('DB_DATABASE', 'afia_orbit'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', '')
    );
    $localPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to local database\n";
} catch (PDOException $e) {
    die("❌ Local database connection failed: " . $e->getMessage() . "\n");
}

// Connect to Render database (PostgreSQL)
try {
    $renderPdo = new PDO(
        "pgsql:host=$renderHost;port=$renderPort;dbname=$renderDatabase",
        $renderUsername,
        $renderPassword
    );
    $renderPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to Render database\n\n";
} catch (PDOException $e) {
    die("❌ Render database connection failed: " . $e->getMessage() . "\n");
}

// Count locations in local database
$countStmt = $localPdo->query("SELECT COUNT(*) FROM tbl_locations");
$totalLocations = $countStmt->fetchColumn();

echo "Found $totalLocations locations in local database\n";
echo "Starting import...\n\n";

// Check if table exists on Render
try {
    $renderPdo->query("SELECT 1 FROM tbl_locations LIMIT 1");
} catch (PDOException $e) {
    die("❌ Table 'tbl_locations' doesn't exist on Render. Please run migrations first.\n");
}

// Clear existing data (optional)
$clearInput = strtolower(readline("Clear existing data on Render? (yes/no): "));
if ($clearInput === 'yes' || $clearInput === 'y') {
    echo "Clearing existing locations...\n";
    $renderPdo->exec("DELETE FROM tbl_locations");
    echo "✅ Cleared\n\n";
}

// Import in batches
$batchSize = 500;
$offset = 0;
$imported = 0;

// Prepare insert statement for PostgreSQL
$insertSql = "INSERT INTO tbl_locations (region, district, ward, street, created_at, updated_at) 
              VALUES (:region, :district, :ward, :street, NOW(), NOW())";
$insertStmt = $renderPdo->prepare($insertSql);

while ($offset < $totalLocations) {
    // Fetch batch from local database
    $stmt = $localPdo->prepare("SELECT region, district, ward, street FROM tbl_locations LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $batchSize, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($locations)) {
        break;
    }
    
    // Insert batch to Render database
    $renderPdo->beginTransaction();
    
    foreach ($locations as $location) {
        $insertStmt->execute([
            ':region' => $location['region'],
            ':district' => $location['district'],
            ':ward' => $location['ward'],
            ':street' => $location['street']
        ]);
        $imported++;
    }
    
    $renderPdo->commit();
    
    $offset += $batchSize;
    $percentage = round(($imported / $totalLocations) * 100, 1);
    echo "\rImported: $imported / $totalLocations ($percentage%)";
}

echo "\n\n✅ Import complete!\n";
echo "Total imported: $imported locations\n\n";

// Verify on Render
$verifyStmt = $renderPdo->query("SELECT COUNT(*) FROM tbl_locations");
$renderCount = $verifyStmt->fetchColumn();

echo "Verification:\n";
echo "  - Local database: $totalLocations locations\n";
echo "  - Render database: $renderCount locations\n";

if ($renderCount == $totalLocations) {
    echo "\n🎉 Success! All locations imported correctly!\n";
} else {
    echo "\n⚠️ Warning: Count mismatch. Please check the import.\n";
}
