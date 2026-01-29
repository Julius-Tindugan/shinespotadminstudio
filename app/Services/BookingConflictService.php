<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\UnavailableDate;
use App\Models\BusinessHour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling booking conflict detection and validation
 */
class BookingConflictService
{
    /**
     * Check if a time slot has any conflicts for a new or updated booking
     *
     * @param string $date Booking date (Y-m-d format)
     * @param string $startTime Start time (H:i format)
     * @param string $endTime End time (H:i format)
     * @param int|null $staffId Staff member ID (optional)
     * @param int|null $excludeBookingId Booking ID to exclude from conflict check (for updates)
     * @return array ['hasConflict' => bool, 'conflicts' => array, 'message' => string]
     */
    public function checkTimeSlotConflicts($date, $startTime, $endTime, $staffId = null, $excludeBookingId = null)
    {
        $conflicts = [];
        $messages = [];

        // 1. Check if date is in unavailable dates range
        $unavailableConflict = $this->checkUnavailableDateConflict($date);
        if ($unavailableConflict['hasConflict']) {
            $conflicts[] = $unavailableConflict;
            $messages[] = $unavailableConflict['message'];
        }

        // 2. Check business hours
        $businessHoursConflict = $this->checkBusinessHoursConflict($date, $startTime, $endTime);
        if ($businessHoursConflict['hasConflict']) {
            $conflicts[] = $businessHoursConflict;
            $messages[] = $businessHoursConflict['message'];
        }

        // 3. Check for overlapping bookings
        $bookingConflict = $this->checkOverlappingBookings($date, $startTime, $endTime, $excludeBookingId);
        if ($bookingConflict['hasConflict']) {
            $conflicts[] = $bookingConflict;
            $messages[] = $bookingConflict['message'];
        }

        // 4. Check staff availability if staff is specified
        if ($staffId) {
            $staffConflict = $this->checkStaffConflicts($staffId, $date, $startTime, $endTime, $excludeBookingId);
            if ($staffConflict['hasConflict']) {
                $conflicts[] = $staffConflict;
                $messages[] = $staffConflict['message'];
            }
        }

        // 5. Check booking slot availability
        $slotConflict = $this->checkBookingSlotAvailability($date, $startTime, $endTime);
        if ($slotConflict['hasConflict']) {
            $conflicts[] = $slotConflict;
            $messages[] = $slotConflict['message'];
        }

        return [
            'hasConflict' => count($conflicts) > 0,
            'conflicts' => $conflicts,
            'message' => implode(' ', $messages)
        ];
    }

    /**
     * Check if date falls within an unavailable date range
     *
     * @param string $date
     * @return array
     */
    private function checkUnavailableDateConflict($date)
    {
        $unavailableDate = UnavailableDate::where(function($query) use ($date) {
            $query->where('start_date', '<=', $date)
                  ->where('end_date', '>=', $date);
        })->first();

        if ($unavailableDate) {
            return [
                'hasConflict' => true,
                'type' => 'unavailable_date',
                'message' => 'This date is marked as unavailable' . ($unavailableDate->reason ? ': ' . $unavailableDate->reason : '.'),
                'details' => $unavailableDate
            ];
        }

        return ['hasConflict' => false];
    }

    /**
     * Check if the booking time falls within business hours
     *
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @return array
     */
    private function checkBusinessHoursConflict($date, $startTime, $endTime)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $businessHour = BusinessHour::where('day_of_week', $dayOfWeek)->first();

        if (!$businessHour || $businessHour->is_closed) {
            return [
                'hasConflict' => true,
                'type' => 'business_closed',
                'message' => 'The studio is closed on this day.',
                'details' => $businessHour
            ];
        }

        $bookingStart = Carbon::parse($startTime);
        $bookingEnd = Carbon::parse($endTime);
        $businessOpen = Carbon::parse($businessHour->open_time);
        $businessClose = Carbon::parse($businessHour->close_time);

        if ($bookingStart->lt($businessOpen) || $bookingEnd->gt($businessClose)) {
            return [
                'hasConflict' => true,
                'type' => 'outside_business_hours',
                'message' => sprintf(
                    'Booking time must be within business hours (%s - %s).',
                    $businessOpen->format('g:i A'),
                    $businessClose->format('g:i A')
                ),
                'details' => [
                    'business_hours' => [
                        'open' => $businessHour->open_time,
                        'close' => $businessHour->close_time
                    ]
                ]
            ];
        }

        return ['hasConflict' => false];
    }

    /**
     * Check for any overlapping bookings on the same date
     *
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeBookingId
     * @return array
     */
    private function checkOverlappingBookings($date, $startTime, $endTime, $excludeBookingId = null)
    {
        $query = Booking::where('booking_date', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where(function ($q) use ($startTime, $endTime) {
                // Check for time overlap
                // Two bookings overlap if: (StartA < EndB) AND (EndA > StartB)
                $q->where(function ($subQ) use ($startTime, $endTime) {
                    $subQ->whereTime('start_time', '<', $endTime)
                         ->whereTime('end_time', '>', $startTime);
                });
            });

        if ($excludeBookingId) {
            $query->where('booking_id', '!=', $excludeBookingId);
        }

        $overlappingBookings = $query->get();

        if ($overlappingBookings->count() > 0) {
            $conflictDetails = $overlappingBookings->map(function ($booking) {
                return [
                    'booking_id' => $booking->booking_id,
                    'booking_reference' => $booking->booking_reference,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'client_name' => $booking->client_first_name . ' ' . $booking->client_last_name
                ];
            })->toArray();

            return [
                'hasConflict' => true,
                'type' => 'overlapping_booking',
                'message' => sprintf(
                    'There %s %d existing booking%s that overlap with this time slot.',
                    $overlappingBookings->count() === 1 ? 'is' : 'are',
                    $overlappingBookings->count(),
                    $overlappingBookings->count() === 1 ? '' : 's'
                ),
                'details' => $conflictDetails
            ];
        }

        return ['hasConflict' => false];
    }

    /**
     * Check if staff member has conflicting bookings
     *
     * @param int $staffId
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeBookingId
     * @return array
     */
    private function checkStaffConflicts($staffId, $date, $startTime, $endTime, $excludeBookingId = null)
    {
        $query = Booking::where('primary_staff_id', $staffId)
            ->where('booking_date', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($subQ) use ($startTime, $endTime) {
                    $subQ->whereTime('start_time', '<', $endTime)
                         ->whereTime('end_time', '>', $startTime);
                });
            });

        if ($excludeBookingId) {
            $query->where('booking_id', '!=', $excludeBookingId);
        }

        $conflictingBookings = $query->get();

        if ($conflictingBookings->count() > 0) {
            return [
                'hasConflict' => true,
                'type' => 'staff_unavailable',
                'message' => sprintf(
                    'The selected staff member has %d conflicting booking%s at this time.',
                    $conflictingBookings->count(),
                    $conflictingBookings->count() === 1 ? '' : 's'
                ),
                'details' => $conflictingBookings->toArray()
            ];
        }

        return ['hasConflict' => false];
    }

    /**
     * Check booking slot availability for the given time range
     *
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @return array
     */
    private function checkBookingSlotAvailability($date, $startTime, $endTime)
    {
        $start = Carbon::parse($date . ' ' . $startTime);
        $end = Carbon::parse($date . ' ' . $endTime);
        
        // Get all time slots that fall within the booking time range
        $affectedSlots = BookingSlot::where('date', $date)
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->whereTime('time_slot', '>=', $startTime)
                      ->whereTime('time_slot', '<', $endTime);
                });
            })
            ->get();

        // Check if any slots are unavailable or fully booked
        $unavailableSlots = $affectedSlots->filter(function($slot) {
            return !$slot->is_available || $slot->isFullyBooked();
        });

        if ($unavailableSlots->count() > 0) {
            return [
                'hasConflict' => true,
                'type' => 'slot_unavailable',
                'message' => sprintf(
                    '%d time slot%s within this booking range %s unavailable or fully booked.',
                    $unavailableSlots->count(),
                    $unavailableSlots->count() === 1 ? '' : 's',
                    $unavailableSlots->count() === 1 ? 'is' : 'are'
                ),
                'details' => $unavailableSlots->map(function($slot) {
                    return [
                        'time_slot' => $slot->time_slot,
                        'formatted_time' => $slot->formatted_time,
                        'is_available' => $slot->is_available,
                        'current_bookings' => $slot->current_bookings,
                        'max_bookings' => $slot->max_bookings
                    ];
                })->toArray()
            ];
        }

        return ['hasConflict' => false];
    }

    /**
     * Reserve booking slots for a time range (atomic operation with locking)
     *
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @return bool
     */
    public function reserveBookingSlots($date, $startTime, $endTime)
    {
        return DB::transaction(function () use ($date, $startTime, $endTime) {
            // Lock the rows for update to prevent race conditions
            $slots = BookingSlot::where('date', $date)
                ->where(function($query) use ($startTime, $endTime) {
                    $query->whereTime('time_slot', '>=', $startTime)
                          ->whereTime('time_slot', '<', $endTime);
                })
                ->lockForUpdate()
                ->get();

            // Check if all slots are available
            foreach ($slots as $slot) {
                if (!$slot->is_available || $slot->isFullyBooked()) {
                    Log::warning('Booking slot unavailable during reservation', [
                        'date' => $date,
                        'time_slot' => $slot->time_slot,
                        'is_available' => $slot->is_available,
                        'current_bookings' => $slot->current_bookings,
                        'max_bookings' => $slot->max_bookings
                    ]);
                    return false;
                }
            }

            // Increment booking count for all slots
            foreach ($slots as $slot) {
                $slot->current_bookings += 1;
                
                // Mark as unavailable if max bookings reached
                if ($slot->max_bookings !== null && $slot->current_bookings >= $slot->max_bookings) {
                    $slot->is_available = false;
                }
                
                $slot->save();
            }

            return true;
        });
    }

    /**
     * Release booking slots when a booking is cancelled or deleted
     *
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @return bool
     */
    public function releaseBookingSlots($date, $startTime, $endTime)
    {
        return DB::transaction(function () use ($date, $startTime, $endTime) {
            $slots = BookingSlot::where('date', $date)
                ->where(function($query) use ($startTime, $endTime) {
                    $query->whereTime('time_slot', '>=', $startTime)
                          ->whereTime('time_slot', '<', $endTime);
                })
                ->lockForUpdate()
                ->get();

            foreach ($slots as $slot) {
                if ($slot->current_bookings > 0) {
                    $slot->current_bookings -= 1;
                }
                
                // Mark as available if under max bookings
                if ($slot->max_bookings === null || $slot->current_bookings < $slot->max_bookings) {
                    $slot->is_available = true;
                }
                
                $slot->save();
            }

            return true;
        });
    }

    /**
     * Validate a complete booking request
     *
     * @param array $bookingData
     * @return array ['isValid' => bool, 'errors' => array]
     */
    public function validateBookingRequest($bookingData)
    {
        $errors = [];

        // Extract booking data
        $date = $bookingData['booking_date'] ?? null;
        $startTime = $bookingData['start_time'] ?? null;
        $endTime = $bookingData['end_time'] ?? null;
        $staffId = $bookingData['primary_staff_id'] ?? null;
        $excludeBookingId = $bookingData['exclude_booking_id'] ?? null;

        // Basic validation
        if (!$date || !$startTime || !$endTime) {
            $errors[] = 'Date, start time, and end time are required.';
            return ['isValid' => false, 'errors' => $errors];
        }

        // Check minimum duration (1 hour)
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        
        if ($start->diffInMinutes($end) < 60) {
            $errors[] = 'Booking duration must be at least 1 hour.';
        }

        // Check for conflicts
        $conflictCheck = $this->checkTimeSlotConflicts(
            $date,
            $startTime,
            $endTime,
            $staffId,
            $excludeBookingId
        );

        if ($conflictCheck['hasConflict']) {
            $errors[] = $conflictCheck['message'];
        }

        return [
            'isValid' => count($errors) === 0,
            'errors' => $errors,
            'conflicts' => $conflictCheck['conflicts'] ?? []
        ];
    }

    /**
     * Synchronize booking slots with actual bookings (maintenance function)
     *
     * @param string $date
     * @return array
     */
    public function synchronizeBookingSlotsForDate($date)
    {
        $updated = 0;
        
        DB::transaction(function () use ($date, &$updated) {
            $slots = BookingSlot::where('date', $date)->lockForUpdate()->get();
            
            foreach ($slots as $slot) {
                $slotTime = Carbon::parse($date . ' ' . $slot->time_slot);
                $slotEndTime = $slotTime->copy()->addMinutes(30);
                
                // Count actual bookings that overlap this slot
                $actualCount = Booking::where('booking_date', $date)
                    ->whereNotIn('status', ['cancelled', 'no_show'])
                    ->where(function($q) use ($slot, $slotEndTime) {
                        $q->whereTime('start_time', '<=', $slot->time_slot)
                          ->whereTime('end_time', '>', $slot->time_slot);
                    })
                    ->count();
                
                if ($slot->current_bookings !== $actualCount) {
                    $slot->current_bookings = $actualCount;
                    
                    // Update availability
                    if ($slot->max_bookings !== null && $actualCount >= $slot->max_bookings) {
                        $slot->is_available = false;
                    } else {
                        $slot->is_available = true;
                    }
                    
                    $slot->save();
                    $updated++;
                }
            }
        });
        
        return [
            'success' => true,
            'slots_updated' => $updated,
            'message' => "Synchronized $updated slot(s) for date $date"
        ];
    }
}
