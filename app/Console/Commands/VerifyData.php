<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerifyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verify-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify data in key tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->checkPackages();
        $this->checkAddons();
        $this->checkBookings();
        $this->checkClients();
        $this->checkStaff();
    }
    
    private function checkPackages()
    {
        $this->info('=== PACKAGES ===');
        $packages = \App\Models\Package::all();
        $this->line('Total packages: ' . $packages->count());
        foreach ($packages->take(5) as $package) {
            $this->line("- {$package->package_name} ({$package->price})");
            $this->line("  Includes {$package->inclusions()->count()} inclusions, {$package->freeItems()->count()} free items");
        }
        $this->newLine();
    }
    
    private function checkAddons()
    {
        $this->info('=== ADDONS ===');
        $addons = \App\Models\Addon::all();
        $this->line('Total addons: ' . $addons->count());
        foreach ($addons->take(5) as $addon) {
            $this->line("- {$addon->addon_name} (Price: {$addon->price})");
        }
        $this->newLine();
    }
    
    private function checkBookings()
    {
        $this->info('=== BOOKINGS ===');
        $bookings = \App\Models\Booking::all();
        $this->line('Total bookings: ' . $bookings->count());
        foreach ($bookings->take(5) as $booking) {
            $this->line("- Booking #{$booking->booking_id} for client #{$booking->client_id}");
            $this->line("  Date: {$booking->booking_date}, Status: {$booking->status}");
            $this->line("  Amount: {$booking->total_amount}");
        }
        $this->newLine();
    }
    
    private function checkClients()
    {
        $this->info('=== CLIENTS ===');
        $clients = \App\Models\Client::all();
        $this->line('Total clients: ' . $clients->count());
        foreach ($clients->take(5) as $client) {
            $this->line("- {$client->first_name} {$client->last_name} ({$client->email})");
        }
        $this->newLine();
    }
    
    private function checkStaff()
    {
        $this->info('=== STAFF ===');
        $staff = \App\Models\Staff::all();
        $this->line('Total staff: ' . $staff->count());
        foreach ($staff->take(5) as $member) {
            $this->line("- {$member->first_name} {$member->last_name} ({$member->position})");
        }
        $this->newLine();
    }
    

}
