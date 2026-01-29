#!/usr/bin/env php
<?php
/**
 * Staff Management System Validation Script
 * 
 * This script validates that all components of the staff management system
 * are properly configured and aligned with the database schema.
 * 
 * Run from the project root: php database/validate_staff_system.php
 */

// Colors for terminal output
$colors = [
    'green' => "\033[32m",
    'red' => "\033[31m",
    'yellow' => "\033[33m",
    'blue' => "\033[34m",
    'reset' => "\033[0m"
];

function printStatus($message, $status, $colors) {
    $icon = $status ? '✓' : '✗';
    $color = $status ? $colors['green'] : $colors['red'];
    echo "{$color}{$icon}{$colors['reset']} {$message}\n";
}

function printSection($title, $colors) {
    echo "\n{$colors['blue']}═══════════════════════════════════════════{$colors['reset']}\n";
    echo "{$colors['blue']}{$title}{$colors['reset']}\n";
    echo "{$colors['blue']}═══════════════════════════════════════════{$colors['reset']}\n\n";
}

echo "\n{$colors['blue']}Staff Management System Validation{$colors['reset']}\n";
echo "══════════════════════════════════════════════════\n\n";

// Load Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$passed = 0;
$failed = 0;

// ============================================
// 1. Database Schema Validation
// ============================================
printSection('1. Database Schema Validation', $colors);

try {
    $columns = DB::select("SHOW COLUMNS FROM staff_users");
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = [
        'staff_id',
        'username',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password_hash',
        'status',
        'admin_id',
        'last_login',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
        'force_password_change',
        'password_changed_at',
        'firebase_uid',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    foreach ($requiredColumns as $column) {
        $exists = in_array($column, $columnNames);
        printStatus("Column '{$column}' exists", $exists, $colors);
        $exists ? $passed++ : $failed++;
    }
    
} catch (Exception $e) {
    printStatus("Database connection failed: " . $e->getMessage(), false, $colors);
    $failed++;
}

// ============================================
// 2. Model Configuration Validation
// ============================================
printSection('2. Staff Model Configuration', $colors);

$staff = new App\Models\Staff();

// Check table name
$tableCorrect = $staff->getTable() === 'staff_users';
printStatus("Table name is 'staff_users'", $tableCorrect, $colors);
$tableCorrect ? $passed++ : $failed++;

// Check primary key
$pkCorrect = $staff->getKeyName() === 'staff_id';
printStatus("Primary key is 'staff_id'", $pkCorrect, $colors);
$pkCorrect ? $passed++ : $failed++;

// Check fillable fields
$fillable = $staff->getFillable();
$requiredFillable = ['username', 'first_name', 'last_name', 'email', 'password_hash'];
foreach ($requiredFillable as $field) {
    $exists = in_array($field, $fillable);
    printStatus("'{$field}' is fillable", $exists, $colors);
    $exists ? $passed++ : $failed++;
}

// Check hidden fields
$hidden = $staff->getHidden();
$passwordHashHidden = in_array('password_hash', $hidden);
printStatus("'password_hash' is hidden", $passwordHashHidden, $colors);
$passwordHashHidden ? $passed++ : $failed++;

// ============================================
// 3. View Files Validation
// ============================================
printSection('3. View Files Validation', $colors);

$viewFiles = [
    'resources/views/settings/modals/create-user.blade.php' => [
        'username field' => ['id="username"', 'name="username"'],
        'first_name field' => ['id="first_name"', 'name="first_name"'],
        'last_name field' => ['id="last_name"', 'name="last_name"'],
        'email field' => ['id="email"', 'name="email"'],
        'password field' => ['id="password"', 'name="password"'],
    ],
    'resources/views/settings/modals/edit-user.blade.php' => [
        'username field' => ['id="edit_username"', 'name="username"'],
        'first_name field' => ['id="edit_first_name"', 'name="first_name"'],
        'last_name field' => ['id="edit_last_name"', 'name="last_name"'],
        'email field' => ['id="edit_email"', 'name="email"'],
    ],
];

foreach ($viewFiles as $file => $checks) {
    $filePath = base_path($file);
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        foreach ($checks as $checkName => $patterns) {
            $allFound = true;
            foreach ($patterns as $pattern) {
                if (strpos($content, $pattern) === false) {
                    $allFound = false;
                    break;
                }
            }
            printStatus("{$file} has {$checkName}", $allFound, $colors);
            $allFound ? $passed++ : $failed++;
        }
    } else {
        printStatus("{$file} exists", false, $colors);
        $failed++;
    }
}

// ============================================
// 4. Service Layer Validation
// ============================================
printSection('4. Service Layer Validation', $colors);

$serviceFile = base_path('app/Services/SettingsService.php');
if (file_exists($serviceFile)) {
    $content = file_get_contents($serviceFile);
    
    $checks = [
        'createStaff has username' => "'username' => \$data['username']",
        'createStaff has password_hash' => "'password_hash' => Hash::make(\$data['password'])",
        'updateStaff has username' => "'username' => \$data['username']",
    ];
    
    foreach ($checks as $checkName => $pattern) {
        $exists = strpos($content, $pattern) !== false;
        printStatus($checkName, $exists, $colors);
        $exists ? $passed++ : $failed++;
    }
} else {
    printStatus("SettingsService.php exists", false, $colors);
    $failed++;
}

// ============================================
// 5. Controller Validation
// ============================================
printSection('5. Controller Validation', $colors);

$controllerFile = base_path('app/Http/Controllers/SettingsController.php');
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    $checks = [
        'storeStaff validates username' => "'username' => 'required|string|min:3|max:50|unique:staff_users,username",
        'getStaff returns username' => "'username' => \$staff->username",
    ];
    
    foreach ($checks as $checkName => $pattern) {
        $exists = strpos($content, $pattern) !== false;
        printStatus($checkName, $exists, $colors);
        $exists ? $passed++ : $failed++;
    }
} else {
    printStatus("SettingsController.php exists", false, $colors);
    $failed++;
}

// ============================================
// 6. JavaScript Validation
// ============================================
printSection('6. JavaScript Validation', $colors);

$jsFile = base_path('public/js/settings.js');
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    $checks = [
        'editStaff populates username' => "getElementById('edit_username').value = staff.username",
    ];
    
    foreach ($checks as $checkName => $pattern) {
        $exists = strpos($content, $pattern) !== false;
        printStatus($checkName, $exists, $colors);
        $exists ? $passed++ : $failed++;
    }
} else {
    printStatus("settings.js exists", false, $colors);
    $failed++;
}

// ============================================
// 7. Authentication Validation
// ============================================
printSection('7. Authentication Configuration', $colors);

$loginFile = base_path('app/Http/Controllers/Auth/LoginController.php');
if (file_exists($loginFile)) {
    $content = file_get_contents($loginFile);
    
    $checks = [
        'Staff login uses username' => "Staff::where('username', \$username)",
        'Staff login checks password_hash' => "Hash::check(\$password, \$localUser->password_hash)",
    ];
    
    foreach ($checks as $checkName => $pattern) {
        $exists = strpos($content, $pattern) !== false;
        printStatus($checkName, $exists, $colors);
        $exists ? $passed++ : $failed++;
    }
} else {
    printStatus("LoginController.php exists", false, $colors);
    $failed++;
}

// ============================================
// 8. Database Data Validation
// ============================================
printSection('8. Database Data Validation', $colors);

try {
    // Check for staff without usernames
    $staffWithoutUsername = DB::table('staff_users')
        ->whereNull('username')
        ->orWhere('username', '')
        ->count();
    
    $noMissingUsernames = $staffWithoutUsername === 0;
    printStatus("All staff have usernames ({$staffWithoutUsername} missing)", $noMissingUsernames, $colors);
    $noMissingUsernames ? $passed++ : $failed++;
    
    // Check for duplicate usernames
    $duplicates = DB::table('staff_users')
        ->select('username', DB::raw('COUNT(*) as count'))
        ->groupBy('username')
        ->having('count', '>', 1)
        ->count();
    
    $noDuplicates = $duplicates === 0;
    printStatus("No duplicate usernames ({$duplicates} duplicates)", $noDuplicates, $colors);
    $noDuplicates ? $passed++ : $failed++;
    
    // Check total staff count
    $totalStaff = DB::table('staff_users')->count();
    printStatus("Total staff members: {$totalStaff}", true, $colors);
    $passed++;
    
} catch (Exception $e) {
    printStatus("Database query failed: " . $e->getMessage(), false, $colors);
    $failed += 3;
}

// ============================================
// Summary
// ============================================
printSection('Validation Summary', $colors);

$total = $passed + $failed;
$percentage = $total > 0 ? round(($passed / $total) * 100, 2) : 0;

echo "Tests Passed: {$colors['green']}{$passed}{$colors['reset']}\n";
echo "Tests Failed: {$colors['red']}{$failed}{$colors['reset']}\n";
echo "Total Tests:  {$total}\n";
echo "Success Rate: ";

if ($percentage >= 90) {
    echo "{$colors['green']}{$percentage}%{$colors['reset']}\n";
} elseif ($percentage >= 70) {
    echo "{$colors['yellow']}{$percentage}%{$colors['reset']}\n";
} else {
    echo "{$colors['red']}{$percentage}%{$colors['reset']}\n";
}

echo "\n";

if ($failed > 0) {
    echo "{$colors['yellow']}⚠ Some tests failed. Please review the issues above.{$colors['reset']}\n";
    echo "{$colors['yellow']}Refer to STAFF_MANAGEMENT_FIXES.md for detailed information.{$colors['reset']}\n\n";
    exit(1);
} else {
    echo "{$colors['green']}✓ All validations passed! The staff management system is properly configured.{$colors['reset']}\n\n";
    exit(0);
}
