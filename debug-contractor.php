<?php
require_once 'bootstrap/app.php';

use App\Models\User;
use App\Models\Contractor;
use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$email = '123@gmail.com';
$password = 'Mauki@2026';

echo "=== Checking Contractor Account ===\n";

// Check if user exists
$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ User with email '{$email}' NOT FOUND in users table\n";
    echo "\nAvailable users:\n";
    User::all(['id', 'email', 'user_type'])->each(fn($u) => echo "  - {$u->email} ({$u->user_type})\n");
} else {
    echo "✅ User FOUND:\n";
    echo "   ID: {$user->id}\n";
    echo "   Email: {$user->email}\n";
    echo "   Type: {$user->user_type}\n";
    echo "   Status: " . ($user->status ?? 'N/A') . "\n";
    
    // Check password
    if (Hash::check($password, $user->password)) {
        echo "✅ Password is CORRECT\n";
    } else {
        echo "❌ Password is INCORRECT\n";
        echo "   Attempting to verify hash...\n";
        echo "   Stored hash: " . substr($user->password, 0, 30) . "...\n";
    }
    
    // Check contractor relationship
    $contractor = Contractor::where('user_id', $user->id)->first();
    if ($contractor) {
        echo "✅ Contractor record EXISTS:\n";
        echo "   ID: {$contractor->id}\n";
        echo "   Status: {$contractor->status}\n";
    } else {
        echo "❌ No contractor record found for this user\n";
    }
}

echo "\n=== Summary ===\n";
