<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed clients
        $this->seedClients();
        
        // Seed staff
        $this->seedStaff();
        
        // Seed bookings
        $this->seedBookings();
        
        // Seed payments
        $this->seedPayments();
    }
    
    /**
     * Seed clients table with demo data.
     */
    protected function seedClients(): void
    {
        if (DB::table('clients')->count() > 0) {
            return; // Skip if data already exists
        }
        
        $clients = [
            [
                'client_id' => 1,
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria.santos@example.com',
                'phone' => '09171234567',
                'address' => '123 Makati Ave, Makati City',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'client_id' => 2,
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@example.com',
                'phone' => '09181234567',
                'address' => '456 Pasig Blvd, Pasig City',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'client_id' => 3,
                'first_name' => 'Ana',
                'last_name' => 'Reyes',
                'email' => 'ana.reyes@example.com',
                'phone' => '09191234567',
                'address' => '789 Quezon Ave, Quezon City',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'client_id' => 4,
                'first_name' => 'Carlos',
                'last_name' => 'Mendoza',
                'email' => 'carlos.mendoza@example.com',
                'phone' => '09201234567',
                'address' => '101 Taft Ave, Manila',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'client_id' => 5,
                'first_name' => 'Isabella',
                'last_name' => 'Garcia',
                'email' => 'isabella.garcia@example.com',
                'phone' => '09211234567',
                'address' => '202 Ortigas Ave, Mandaluyong City',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        
        DB::table('clients')->insert($clients);
    }
    
    /**
     * Seed staff table with demo data.
     */
    protected function seedStaff(): void
    {
        if (DB::table('staff_users')->count() > 2) { // Assuming at least 1-2 staff already exist from AdminUserSeeder
            return; // Skip if data already exists
        }
        
        $staff = [
            [
                'staff_id' => 3, // Starting from 3 as 1-2 might exist
                'first_name' => 'Sofia',
                'last_name' => 'Lim',
                'email' => 'sofia.lim@shinespot.com',
                'phone' => '09321234567',
                'position' => 'Senior Hair Stylist',
                'status' => 'active',
                'password_hash' => bcrypt('password'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'staff_id' => 4,
                'first_name' => 'Miguel',
                'last_name' => 'Tan',
                'email' => 'miguel.tan@shinespot.com',
                'phone' => '09331234567',
                'position' => 'Nail Technician',
                'status' => 'active',
                'password_hash' => bcrypt('password'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'staff_id' => 5,
                'first_name' => 'Camille',
                'last_name' => 'Ramos',
                'email' => 'camille.ramos@shinespot.com',
                'phone' => '09341234567',
                'position' => 'Massage Therapist',
                'status' => 'active',
                'password_hash' => bcrypt('password'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        
        DB::table('staff_users')->insert($staff);
    }
    

    
    /**
     * Seed bookings table with demo data.
     */
    protected function seedBookings(): void
    {
        if (DB::table('bookings')->count() > 0) {
            return; // Skip if data already exists
        }
        
        $today = Carbon::today();
        
        // Get admin user ID for created_by field
        $adminId = DB::table('admin_users')->where('email', 'admin@shinespot.com')->value('admin_id') ?? 1;
        
        $bookings = [
            [
                'booking_id' => 1,
                'client_id' => 1,
                'primary_staff_id' => 3,
                'booking_date' => $today->copy()->addDays(1),
                'start_time' => $today->copy()->addDays(1)->setTime(10, 0),
                'end_time' => $today->copy()->addDays(1)->setTime(11, 30),
                'status' => 'confirmed',
                'total_amount' => 2850.00,
                'notes' => 'Client prefers Sofia as the assigned staff.',
                'created_by' => $adminId,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'booking_id' => 2,
                'client_id' => 2,
                'primary_staff_id' => 4,
                'booking_date' => $today->copy()->addDays(2),
                'start_time' => $today->copy()->addDays(2)->setTime(14, 0),
                'end_time' => $today->copy()->addDays(2)->setTime(15, 45),
                'status' => 'pending',
                'total_amount' => 800.00,
                'notes' => 'First-time client, prefers gentle nail service.',
                'created_by' => $adminId,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'booking_id' => 3,
                'client_id' => 3,
                'primary_staff_id' => 5,
                'booking_date' => $today->copy()->addDays(3),
                'start_time' => $today->copy()->addDays(3)->setTime(16, 30),
                'end_time' => $today->copy()->addDays(3)->setTime(18, 0),
                'status' => 'confirmed',
                'total_amount' => 1200.00,
                'notes' => 'Client mentioned back pain, focus on upper back.',
                'created_by' => $adminId,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'booking_id' => 4,
                'client_id' => 4,
                'primary_staff_id' => 3,
                'booking_date' => $today->copy()->addDays(4),
                'start_time' => $today->copy()->addDays(4)->setTime(9, 0),
                'end_time' => $today->copy()->addDays(4)->setTime(10, 0),
                'status' => 'confirmed',
                'total_amount' => 500.00,
                'notes' => 'Regular client, prefers short haircut.',
                'created_by' => $adminId,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'booking_id' => 5,
                'client_id' => 5,
                'primary_staff_id' => 4,
                'booking_date' => $today->copy()->addDays(5),
                'start_time' => $today->copy()->addDays(5)->setTime(13, 0),
                'end_time' => $today->copy()->addDays(5)->setTime(15, 0),
                'status' => 'pending',
                'total_amount' => 2950.00,
                'notes' => 'Complete nail and hair service package.',
                'created_by' => $adminId,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now(),
            ],
        ];
        
        DB::table('bookings')->insert($bookings);
    }
    

    
    /**
     * Seed payments table with demo data.
     */
    protected function seedPayments(): void
    {
        if (DB::table('payments')->count() > 0) {
            return; // Skip if data already exists
        }
        
        $payments = [
            // Payment for Booking 1
            [
                'payment_id' => 1,
                'booking_id' => 1,
                'amount' => 500.00,
                'payment_date' => Carbon::now()->subDays(2),
                'payment_method' => 'gcash',
                'status' => 'completed',
                'reference_number' => 'GC' . Str::random(8),
                'notes' => 'Deposit payment',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            
            // Payment for Booking 3
            [
                'payment_id' => 2,
                'booking_id' => 3,
                'amount' => 300.00,
                'payment_date' => Carbon::now()->subDays(3),
                'payment_method' => 'credit_card',
                'status' => 'completed',
                'reference_number' => 'CC' . Str::random(8),
                'notes' => 'Deposit payment',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            
            // Payment for Booking 4
            [
                'payment_id' => 3,
                'booking_id' => 4,
                'amount' => 200.00,
                'payment_date' => Carbon::now()->subDays(1),
                'payment_method' => 'cash',
                'status' => 'completed',
                'reference_number' => 'CA' . Str::random(8),
                'notes' => 'Deposit payment',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            
            // Payment for Booking 5
            [
                'payment_id' => 4,
                'booking_id' => 5,
                'amount' => 1000.00,
                'payment_date' => Carbon::now()->subHours(12),
                'payment_method' => 'gcash',
                'status' => 'completed',
                'reference_number' => 'GC' . Str::random(8),
                'notes' => 'Deposit payment',
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ],
        ];
        
        DB::table('payments')->insert($payments);
    }
}
