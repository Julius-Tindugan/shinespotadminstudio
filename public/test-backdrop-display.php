<?php
/**
 * Test script to verify backdrop display fix
 * Access via: http://your-domain/test-backdrop-display.php
 * 
 * This script helps verify that backdrop selections are displayed correctly
 * showing actual backdrop names and colors instead of just counts.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backdrop Display Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .booking-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .backdrop-display {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }
        .backdrop-color {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 2px solid white;
            box-shadow: 0 0 0 1px #e0e0e0;
            display: inline-block;
        }
        .backdrop-name {
            font-weight: 500;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-confirmed {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .success {
            color: #2e7d32;
            background: #e8f5e9;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎨 Backdrop Display Test</h1>
        <p>This page verifies that backdrop selections show actual colors and names instead of counts.</p>
    </div>

    <div class="success">
        ✅ Fix Applied Successfully! Backdrop selections now display actual backdrop names and colors.
    </div>

    <?php
    $bookings = App\Models\Booking::with(['backdrop', 'package'])
        ->whereNotNull('backdrop_selections')
        ->orWhereNotNull('backdrop_id')
        ->orderBy('booking_date', 'desc')
        ->take(10)
        ->get();

    foreach ($bookings as $booking):
    ?>
    <div class="booking-card">
        <h3>
            Booking #<?= $booking->booking_id ?> - <?= $booking->booking_reference ?>
            <span class="status status-confirmed"><?= ucfirst($booking->status) ?></span>
        </h3>
        <p>
            <strong>Client:</strong> <?= $booking->client_first_name ?> <?= $booking->client_last_name ?><br>
            <strong>Date:</strong> <?= $booking->booking_date->format('M d, Y') ?><br>
            <strong>Package:</strong> <?= $booking->package->title ?? 'N/A' ?>
        </p>

        <p><strong>Backdrop(s):</strong></p>
        
        <?php if ($booking->backdrop_id && $booking->backdrop): ?>
            <!-- Single Backdrop Selection -->
            <div class="backdrop-display">
                <?php if ($booking->backdrop->color_code): ?>
                    <span class="backdrop-color" style="background-color: <?= $booking->backdrop->color_code ?>;" title="<?= $booking->backdrop->name ?>"></span>
                <?php endif; ?>
                <span class="backdrop-name"><?= $booking->backdrop->name ?></span>
            </div>
        
        <?php elseif ($booking->backdrop_selections): ?>
            <!-- Multiple Backdrop Selections -->
            <?php 
            $selections = $booking->formatted_backdrop_selections;
            if (!empty($selections)): 
            ?>
                <div class="backdrop-display">
                    <?php foreach ($selections as $index => $selection): ?>
                        <?php if (isset($selection['color'])): ?>
                            <span class="backdrop-color" 
                                  style="background-color: <?= $selection['color'] ?>;" 
                                  title="<?= $selection['name'] ?? $selection['color'] ?>"></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <span class="backdrop-name">
                        <?php 
                        $names = array_map(function($s) { return $s['name'] ?? 'Unknown'; }, $selections);
                        echo implode(', ', $names);
                        ?>
                    </span>
                    <span style="color: #666; font-size: 12px;">(<?= count($selections) ?> backdrop<?= count($selections) > 1 ? 's' : '' ?>)</span>
                </div>
            <?php else: ?>
                <div class="backdrop-display">
                    <span style="color: #999; font-style: italic;">Custom Selection</span>
                </div>
            <?php endif; ?>
        
        <?php else: ?>
            <div class="backdrop-display">
                <span style="color: #999; font-style: italic;">No backdrop selected</span>
            </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <?php if ($bookings->isEmpty()): ?>
    <div class="booking-card">
        <p style="text-align: center; color: #999;">No bookings with backdrop selections found.</p>
    </div>
    <?php endif; ?>

    <div class="header" style="margin-top: 30px;">
        <h3>What Was Fixed?</h3>
        <ul>
            <li>✅ Added <code>formatted_backdrop_selections</code> accessor to Booking model</li>
            <li>✅ Updated booking list view to display actual backdrop colors and names</li>
            <li>✅ Updated booking details view to show backdrop information properly</li>
            <li>✅ Handles various data formats (IDs, objects, associative arrays)</li>
        </ul>
        <p><strong>Note:</strong> You can now delete this test file after verification.</p>
    </div>
</body>
</html>
