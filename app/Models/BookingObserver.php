<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\BookingConflictService;

class BookingObserver
{
    /**
     * The booking conflict service instance.
     *
     * @var \App\Services\BookingConflictService
     */
    protected $conflictService;

    /**
     * Create a new observer instance.
     */
    public function __construct()
    {
        $this->conflictService = app(BookingConflictService::class);
    }

    /**
     * Handle the Booking "creating" event.
     * This runs before a new booking is created.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function creating(Booking $booking)
    {
        // Reserve booking slots when creating a new booking
        if ($booking->booking_date && $booking->start_time && $booking->end_time) {
            $date = $booking->booking_date instanceof Carbon 
                ? $booking->booking_date->format('Y-m-d') 
                : Carbon::parse($booking->booking_date)->format('Y-m-d');
            
            $startTime = Carbon::parse($booking->start_time)->format('H:i:00');
            $endTime = Carbon::parse($booking->end_time)->format('H:i:00');
            
            $success = $this->conflictService->reserveBookingSlots($date, $startTime, $endTime);
            
            if (!$success) {
                Log::warning('Failed to reserve booking slots', [
                    'booking_date' => $date,
                    'start_time' => $startTime,
                    'end_time' => $endTime
                ]);
            }
        }
    }

    /**
     * Handle the Booking "updating" event.
     * This runs before a booking is updated.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function updating(Booking $booking)
    {
        // If date or time changed, release old slots and reserve new ones
        if ($booking->isDirty(['booking_date', 'start_time', 'end_time', 'status'])) {
            $original = $booking->getOriginal();
            
            // If status changed to cancelled or no_show, release slots
            if ($booking->isDirty('status') && in_array($booking->status, ['cancelled', 'no_show'])) {
                $this->releaseBookingSlotsForBooking($booking);
            }
            // If date/time changed, handle slot reallocation
            elseif ($booking->isDirty(['booking_date', 'start_time', 'end_time'])) {
                // Release old slots
                if (isset($original['booking_date']) && isset($original['start_time']) && isset($original['end_time'])) {
                    $oldDate = $original['booking_date'] instanceof Carbon 
                        ? $original['booking_date']->format('Y-m-d') 
                        : Carbon::parse($original['booking_date'])->format('Y-m-d');
                    $oldStartTime = Carbon::parse($original['start_time'])->format('H:i:00');
                    $oldEndTime = Carbon::parse($original['end_time'])->format('H:i:00');
                    
                    $this->conflictService->releaseBookingSlots($oldDate, $oldStartTime, $oldEndTime);
                }
                
                // Reserve new slots (only if not cancelled/no_show)
                if (!in_array($booking->status, ['cancelled', 'no_show'])) {
                    $newDate = $booking->booking_date instanceof Carbon 
                        ? $booking->booking_date->format('Y-m-d') 
                        : Carbon::parse($booking->booking_date)->format('Y-m-d');
                    $newStartTime = Carbon::parse($booking->start_time)->format('H:i:00');
                    $newEndTime = Carbon::parse($booking->end_time)->format('H:i:00');
                    
                    $this->conflictService->reserveBookingSlots($newDate, $newStartTime, $newEndTime);
                }
            }
        }
    }

    /**
     * Handle the Booking "deleted" event.
     * This runs after a booking is deleted.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function deleted(Booking $booking)
    {
        // Release booking slots when a booking is deleted
        $this->releaseBookingSlotsForBooking($booking);
    }

    /**
     * Release booking slots for a booking
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    protected function releaseBookingSlotsForBooking(Booking $booking)
    {
        if ($booking->booking_date && $booking->start_time && $booking->end_time) {
            $date = $booking->booking_date instanceof Carbon 
                ? $booking->booking_date->format('Y-m-d') 
                : Carbon::parse($booking->booking_date)->format('Y-m-d');
            
            $startTime = Carbon::parse($booking->start_time)->format('H:i:00');
            $endTime = Carbon::parse($booking->end_time)->format('H:i:00');
            
            $this->conflictService->releaseBookingSlots($date, $startTime, $endTime);
            
            Log::info('Released booking slots', [
                'booking_id' => $booking->booking_id,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime
            ]);
        }
    }

    /**
     * Handle the Booking "saving" event.
     * This runs before create or update.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function saving(Booking $booking)
    {
        $this->autoUpdateBookingStatus($booking);
    }

    /**
     * Handle the Booking "retrieved" event.
     * This runs when a booking is loaded from the database.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function retrieved(Booking $booking)
    {
        // Only auto-update if the booking needs updating
        if ($this->needsStatusUpdate($booking)) {
            $this->autoUpdateBookingStatus($booking);
            // Save silently without triggering events again
            $booking->saveQuietly();
        }
    }

    /**
     * Automatically update booking status based on payment status and booking date.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    protected function autoUpdateBookingStatus(Booking $booking)
    {
        // Don't update if already cancelled or no_show
        if (in_array($booking->status, ['cancelled', 'no_show'])) {
            return;
        }

        $originalStatus = $booking->status;
        $originalPaymentStatus = $booking->getOriginal('payment_status');

        // Rule 1: If payment_status is 'paid', set status to 'confirmed'
        if ($booking->payment_status === 'paid' && $booking->status === 'pending') {
            $booking->status = 'confirmed';
            Log::info("Booking #{$booking->booking_id}: Status auto-updated from pending to confirmed", [
                'reason' => 'payment received',
                'payment_status' => $booking->payment_status,
                'old_status' => $originalStatus,
                'new_status' => 'confirmed'
            ]);
        }

        // Rule 2: If booking date has passed, set status to 'completed'
        if ($booking->booking_date && $booking->end_time) {
            $bookingDateTime = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . Carbon::parse($booking->end_time)->format('H:i:s'));
            
            if ($bookingDateTime->isPast() && !in_array($booking->status, ['completed', 'cancelled', 'no_show'])) {
                $booking->status = 'completed';
                Log::info("Booking #{$booking->booking_id}: Status auto-updated to completed", [
                    'reason' => 'booking date passed',
                    'booking_datetime' => $bookingDateTime->toIso8601String(),
                    'old_status' => $originalStatus,
                    'new_status' => 'completed'
                ]);
            }
        }

        // Log if status changed
        if ($originalStatus !== $booking->status && $booking->exists) {
            Log::info("Booking #{$booking->booking_id}: Status changed", [
                'from' => $originalStatus,
                'to' => $booking->status,
                'payment_status' => $booking->payment_status,
                'payment_status_changed' => ($originalPaymentStatus !== $booking->payment_status),
                'old_payment_status' => $originalPaymentStatus
            ]);
        }
    }

    /**
     * Check if booking needs status update.
     *
     * @param  \App\Models\Booking  $booking
     * @return bool
     */
    protected function needsStatusUpdate(Booking $booking): bool
    {
        // Skip if cancelled or no_show
        if (in_array($booking->status, ['cancelled', 'no_show'])) {
            return false;
        }

        // Check if payment is paid but status is still pending
        if ($booking->payment_status === 'paid' && $booking->status === 'pending') {
            return true;
        }

        // Check if booking date has passed but status is not completed
        if ($booking->booking_date && $booking->end_time) {
            $bookingDateTime = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . Carbon::parse($booking->end_time)->format('H:i:s'));
            
            if ($bookingDateTime->isPast() && !in_array($booking->status, ['completed', 'cancelled', 'no_show'])) {
                return true;
            }
        }

        return false;
    }
}
