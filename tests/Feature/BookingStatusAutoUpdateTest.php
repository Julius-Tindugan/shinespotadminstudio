<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingStatusAutoUpdateTest extends TestCase
{
    /**
     * Test that booking status changes to confirmed when payment is paid
     */
    public function test_booking_status_changes_to_confirmed_when_paid()
    {
        // Create a pending booking
        $booking = Booking::factory()->create([
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'booking_date' => Carbon::tomorrow(),
        ]);

        // Update payment status to paid
        $booking->payment_status = 'paid';
        $booking->save();

        // Refresh from database
        $booking->refresh();

        // Assert status changed to confirmed
        $this->assertEquals('confirmed', $booking->status);
    }

    /**
     * Test that booking status changes to completed when date passes
     */
    public function test_booking_status_changes_to_completed_when_date_passes()
    {
        // Create a confirmed booking for yesterday
        $booking = Booking::factory()->create([
            'status' => 'confirmed',
            'booking_date' => Carbon::yesterday(),
            'end_time' => Carbon::yesterday()->setTime(18, 0, 0), // 6 PM yesterday
        ]);

        // Retrieve the booking (triggers observer)
        $retrievedBooking = Booking::find($booking->booking_id);

        // Assert status changed to completed
        $this->assertEquals('completed', $retrievedBooking->status);
    }

    /**
     * Test that cancelled bookings are not auto-updated
     */
    public function test_cancelled_bookings_are_not_auto_updated()
    {
        // Create a cancelled booking with paid status
        $booking = Booking::factory()->create([
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
        ]);

        // Try to update payment status
        $booking->payment_status = 'paid';
        $booking->save();

        // Refresh and assert status is still cancelled
        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);
    }

    /**
     * Test that no_show bookings are not auto-updated
     */
    public function test_no_show_bookings_are_not_auto_updated()
    {
        // Create a no_show booking
        $booking = Booking::factory()->create([
            'status' => 'no_show',
            'booking_date' => Carbon::yesterday(),
            'end_time' => Carbon::yesterday()->setTime(18, 0, 0),
        ]);

        // Retrieve the booking
        $retrievedBooking = Booking::find($booking->booking_id);

        // Assert status is still no_show
        $this->assertEquals('no_show', $retrievedBooking->status);
    }
}
