<?php

/**
 * Script to generate usernames for existing staff users who don't have one
 * This fixes the password reset login issue where staff can't login after resetting password
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "==========================================\n";
echo "Fixing Staff Usernames\n";
echo "==========================================\n\n";

try {
    // Get all staff users without username or with null username
    $staffWithoutUsername = DB::table('staff_users')
        ->whereNull('username')
        ->orWhere('username', '')
        ->get();
    
    if ($staffWithoutUsername->isEmpty()) {
        echo "✓ All staff users already have usernames!\n";
        exit(0);
    }
    
    echo "Found " . $staffWithoutUsername->count() . " staff user(s) without username.\n\n";
    
    $updated = 0;
    $errors = 0;
    
    foreach ($staffWithoutUsername as $staff) {
        // Generate username from email or first/last name
        $baseUsername = null;
        
        // Try to use email prefix first
        if ($staff->email) {
            $baseUsername = strtolower(explode('@', $staff->email)[0]);
        }
        
        // If no email or email-based username fails, use name
        if (!$baseUsername && $staff->first_name && $staff->last_name) {
            $baseUsername = strtolower($staff->first_name . $staff->last_name);
        }
        
        // If still no username, use staff ID
        if (!$baseUsername) {
            $baseUsername = 'staff' . $staff->staff_id;
        }
        
        // Remove special characters and spaces
        $baseUsername = preg_replace('/[^a-z0-9._-]/', '', $baseUsername);
        
        // Ensure username is unique
        $username = $baseUsername;
        $counter = 1;
        
        while (DB::table('staff_users')->where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        // Also check admin_users table to avoid conflicts
        while (DB::table('admin_users')->where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        try {
            // Update the staff user with the new username
            DB::table('staff_users')
                ->where('staff_id', $staff->staff_id)
                ->update([
                    'username' => $username,
                    'updated_at' => now()
                ]);
            
            echo "✓ Staff ID {$staff->staff_id}: {$staff->first_name} {$staff->last_name}\n";
            echo "  Email: {$staff->email}\n";
            echo "  Username: {$username}\n\n";
            
            $updated++;
            
        } catch (\Exception $e) {
            echo "✗ Failed to update staff ID {$staff->staff_id}: {$e->getMessage()}\n\n";
            $errors++;
        }
    }
    
    echo "==========================================\n";
    echo "Summary:\n";
    echo "  - Updated: {$updated}\n";
    echo "  - Errors: {$errors}\n";
    echo "==========================================\n\n";
    
    if ($updated > 0) {
        echo "SUCCESS! Staff users can now login with their usernames.\n";
        echo "They can also use email for password reset.\n\n";
        echo "IMPORTANT: Notify affected staff members of their new usernames!\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
