<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateBookingStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:update-statuses 
                            {--dry-run : Run without making any changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update booking statuses based on payment status and booking dates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
        }

        $this->info('Checking bookings for status updates...');
        
        $updatedCount = 0;
        $toConfirmedCount = 0;
        $toCompletedCount = 0;

        // Rule 1: Update pending bookings with paid payment_status to confirmed
        $this->line("\n--- Checking for paid bookings in pending status ---");
        $paidPendingBookings = Booking::where('payment_status', 'paid')
            ->where('status', 'pending')
            ->get();

        if ($paidPendingBookings->count() > 0) {
            $this->info("Found {$paidPendingBookings->count()} paid booking(s) with pending status");
            
            foreach ($paidPendingBookings as $booking) {
                $this->line("  Booking #{$booking->booking_id} - {$booking->client_name} - {$booking->booking_date->format('Y-m-d')}");
                
                if (!$isDryRun) {
                    $booking->status = 'confirmed';
                    $booking->save();
                    Log::info("Command: Updated booking #{$booking->booking_id} from pending to confirmed (paid)");
                }
                
                $updatedCount++;
                $toConfirmedCount++;
            }
        } else {
            $this->line("  No paid pending bookings found");
        }

        // Rule 2: Update past bookings to completed
        $this->line("\n--- Checking for past bookings ---");
        $pastBookings = Booking::whereNotIn('status', ['completed', 'cancelled', 'no_show'])
            ->whereNotNull('booking_date')
            ->whereNotNull('end_time')
            ->get()
            ->filter(function($booking) {
                // Check if booking end time has passed
                $bookingDateTime = Carbon::parse(
                    $booking->booking_date->format('Y-m-d') . ' ' . 
                    Carbon::parse($booking->end_time)->format('H:i:s')
                );
                return $bookingDateTime->isPast();
            });

        if ($pastBookings->count() > 0) {
            $this->info("Found {$pastBookings->count()} past booking(s) that should be completed");
            
            foreach ($pastBookings as $booking) {
                $bookingDateTime = Carbon::parse(
                    $booking->booking_date->format('Y-m-d') . ' ' . 
                    Carbon::parse($booking->end_time)->format('H:i:s')
                );
                
                $this->line("  Booking #{$booking->booking_id} - {$booking->client_name} - {$bookingDateTime->format('Y-m-d H:i')} ({$bookingDateTime->diffForHumans()})");
                
                if (!$isDryRun) {
                    $oldStatus = $booking->status;
                    $booking->status = 'completed';
                    $booking->save();
                    Log::info("Command: Updated booking #{$booking->booking_id} from {$oldStatus} to completed (date passed)");
                }
                
                $updatedCount++;
                $toCompletedCount++;
            }
        } else {
            $this->line("  No past bookings found");
        }

        // Summary
        $this->newLine();
        if ($updatedCount > 0) {
            if ($isDryRun) {
                $this->warn("DRY RUN: Would update {$updatedCount} booking(s):");
            } else {
                $this->info("Successfully updated {$updatedCount} booking(s):");
            }
            $this->line("  - {$toConfirmedCount} updated to 'confirmed' (payment received)");
            $this->line("  - {$toCompletedCount} updated to 'completed' (date passed)");
        } else {
            $this->info('No bookings needed status updates');
        }

        return Command::SUCCESS;
    }
}
