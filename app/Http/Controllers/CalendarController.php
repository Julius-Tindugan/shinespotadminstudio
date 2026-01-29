<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\BusinessHour;
use App\Models\UnavailableDate;
use App\Models\BookingSlot;
use App\Models\Booking;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    /**
     * Display the calendar management page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Initialize business hours if they don't exist
        $this->initializeBusinessHours();
        
        return view('calendar.index');
    }

    /**
     * Initialize business hours for all days of the week
     */
    private function initializeBusinessHours()
    {
        for ($i = 0; $i < 7; $i++) {
            BusinessHour::updateOrCreate(
                ['day_of_week' => $i],
                [
                    'open_time' => '09:00:00',
                    'close_time' => '17:00:00',
                    'is_closed' => ($i === 0 || $i === 6) // Closed on weekends by default
                ]
            );
        }
    }

    /**
     * Get all bookings for calendar display
     * Returns ALL bookings (past, present, and future) for complete historical view
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookings()
    {
        try {
            // Fetch ALL bookings with relationships, ensuring data integrity
            // NOTE: We intentionally fetch all bookings regardless of date
            // to display complete booking history on the calendar
            $bookings = Booking::with(['package', 'primaryStaff'])
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->whereNotNull('booking_date')
                ->get()
                ->filter(function ($booking) {
                    // Additional validation: ensure booking has valid data
                    return $booking->booking_id 
                        && $booking->start_time 
                        && $booking->end_time;
                })
                ->map(function ($booking) {
                    // Define status colors matching the system theme and legend
                    // Default to green for confirmed status
                    $backgroundColor = '#22c55e'; // Green for confirmed
                    $borderColor = '#16a34a';
                    $textColor = '#ffffff';
                    
                    // Use case-insensitive comparison
                    $status = strtolower($booking->status ?? 'pending');
                    
                    if ($status === 'pending') {
                        $backgroundColor = '#eab308'; // Yellow for pending
                        $borderColor = '#ca8a04';
                        $textColor = '#ffffff';
                    } elseif ($status === 'cancelled') {
                        $backgroundColor = '#ef4444'; // Red for cancelled
                        $borderColor = '#dc2626';
                        $textColor = '#ffffff';
                    } elseif ($status === 'completed') {
                        $backgroundColor = '#10b981'; // Emerald for completed
                        $borderColor = '#059669';
                        $textColor = '#ffffff';
                    }

                    // Build title with null safety
                    $title = $booking->package ? $booking->package->package_name : 'Booking';
                    if ($booking->client_first_name && $booking->client_last_name) {
                        $title .= ' - ' . $booking->client_first_name . ' ' . $booking->client_last_name;
                    }

                    // Ensure proper datetime format for FullCalendar (ISO 8601)
                    // Combine booking_date with start_time and end_time
                    if ($booking->booking_date && $booking->start_time && $booking->end_time) {
                        $bookingDate = Carbon::parse($booking->booking_date);
                        
                        // Parse times and combine with the booking date
                        $startTimeParsed = Carbon::parse($booking->start_time);
                        $endTimeParsed = Carbon::parse($booking->end_time);
                        
                        // Create full datetime by combining date and time components
                        $startDateTime = $bookingDate->copy()
                            ->setTime($startTimeParsed->hour, $startTimeParsed->minute, $startTimeParsed->second);
                        $endDateTime = $bookingDate->copy()
                            ->setTime($endTimeParsed->hour, $endTimeParsed->minute, $endTimeParsed->second);
                        
                        // Format as ISO 8601 for FullCalendar
                        $startTime = $startDateTime->toIso8601String();
                        $endTime = $endDateTime->toIso8601String();
                    } else {
                        // Fallback if data is incomplete
                        $startTime = $booking->start_time;
                        $endTime = $booking->end_time;
                    }

                    return [
                        'id' => $booking->booking_id,
                        'title' => $title,
                        'start' => $startTime,
                        'end' => $endTime,
                        'backgroundColor' => $backgroundColor,
                        'borderColor' => $borderColor,
                        'textColor' => $textColor,
                        'extendedProps' => [
                            'booking_id' => $booking->booking_id,
                            'client_name' => ($booking->client_first_name && $booking->client_last_name) 
                                ? $booking->client_first_name . ' ' . $booking->client_last_name 
                                : 'Unknown Client',
                            'service' => $booking->package ? $booking->package->package_name : 'No Package',
                            'status' => $booking->status ?? 'pending',
                            'type' => 'booking'
                        ]
                    ];
                })
                ->values(); // Reset array keys

            \Log::info('Calendar bookings fetched', [
                'total_count' => $bookings->count(),
                'booking_ids' => $bookings->pluck('id')->toArray()
            ]);

            return response()->json($bookings);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching calendar bookings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to load bookings',
                'message' => 'Unable to retrieve booking data. Please try again later.'
            ], 500);
        }
    }

    /**
     * Get all unavailable dates for calendar display
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnavailableDates()
    {
        $unavailableDates = UnavailableDate::get()
            ->map(function ($unavailable) {
                $startDate = Carbon::parse($unavailable->start_date);
                $endDate = Carbon::parse($unavailable->end_date)->addDay(); // Add 1 day to make it inclusive in FullCalendar

                return [
                    'id' => 'unavailable-' . $unavailable->date_id,
                    'title' => $unavailable->reason ?: 'Studio Unavailable',
                    'start' => $startDate->toDateString(),
                    'end' => $endDate->toDateString(),
                    'backgroundColor' => 'rgba(148, 163, 184, 0.25)',
                    'borderColor' => 'rgba(100, 116, 139, 0.6)',
                    'textColor' => '#475569',
                    'allDay' => true,
                    'classNames' => ['unavailable-date'],
                    'extendedProps' => [
                        'unavailable_id' => $unavailable->date_id,
                        'reason' => $unavailable->reason,
                        'type' => 'unavailable'
                    ]
                ];
            });

        return response()->json($unavailableDates);
    }

    /**
     * Get all business hours
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBusinessHours()
    {
        $businessHours = BusinessHour::orderBy('day_of_week')->get()
            ->map(function ($hours) {
                // Format for FullCalendar business hours
                $dayOfWeek = $hours->day_of_week;
                
                // If the day is marked as closed, it's not a business day
                if ($hours->is_closed) {
                    return null;
                }
                
                return [
                    'daysOfWeek' => [$dayOfWeek],
                    'startTime' => Carbon::parse($hours->open_time)->format('H:i'),
                    'endTime' => Carbon::parse($hours->close_time)->format('H:i')
                ];
            })
            ->filter(); // Remove null values

        return response()->json($businessHours);
    }

    /**
     * Get all business hours for display and editing
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBusinessHours()
    {
        $businessHours = BusinessHour::orderBy('day_of_week')->get();
        return response()->json($businessHours);
    }

    /**
     * Update business hours
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBusinessHours(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hours' => 'required|array',
            'hours.*.day_of_week' => 'required|integer|between:0,6',
            'hours.*.is_closed' => 'required|boolean',
            'hours.*.open_time' => 'required_if:hours.*.is_closed,0|nullable|date_format:H:i',
            'hours.*.close_time' => 'required_if:hours.*.is_closed,0|nullable|date_format:H:i|after:hours.*.open_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Begin transaction
        DB::beginTransaction();
        
        try {
            foreach ($request->hours as $hour) {
                BusinessHour::updateOrCreate(
                    ['day_of_week' => $hour['day_of_week']],
                    [
                        'open_time' => $hour['is_closed'] ? null : $hour['open_time'],
                        'close_time' => $hour['is_closed'] ? null : $hour['close_time'],
                        'is_closed' => $hour['is_closed']
                    ]
                );
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Business hours updated successfully',
                'hours' => BusinessHour::orderBy('day_of_week')->get()
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update business hours: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new unavailable date
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeUnavailableDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if there are any existing bookings in the date range
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        $existingBookings = Booking::whereDate('start_time', '>=', $startDate)
            ->whereDate('start_time', '<=', $endDate)
            ->where('status', '!=', 'cancelled')
            ->count();
            
        if ($existingBookings > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot mark dates as unavailable: There are {$existingBookings} existing bookings in this date range."
            ], 422);
        }
        
        // Create unavailable date
        try {
            $unavailable = UnavailableDate::create([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'created_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Dates marked as unavailable successfully',
                'unavailable' => $unavailable
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark dates as unavailable: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove an unavailable date
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeUnavailableDate($id)
    {
        try {
            // Add validation to check if ID is provided
            if (empty($id) || $id === 'undefined') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid unavailable date ID provided'
                ], 400);
            }

            $unavailable = UnavailableDate::findOrFail($id);
            $unavailable->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Unavailable date removed successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unavailable date not found with ID: ' . $id
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove unavailable date: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booking slots for a specific date
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookingSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek;
        
        // Check if date is in unavailable dates
        $isUnavailable = UnavailableDate::where(function($query) use ($date) {
            $query->where('start_date', '<=', $date)
                  ->where('end_date', '>=', $date);
        })->exists();
        
        if ($isUnavailable) {
            return response()->json([
                'success' => true,
                'available' => false,
                'message' => 'This date is marked as unavailable',
                'slots' => []
            ]);
        }
        
        // Get business hours for this day
        $businessHour = BusinessHour::where('day_of_week', $dayOfWeek)->first();
        
        if (!$businessHour || $businessHour->is_closed) {
            return response()->json([
                'success' => true,
                'available' => false,
                'message' => 'The studio is closed on this day',
                'slots' => []
            ]);
        }
        
        // Get all booking slots for this date
        $slots = BookingSlot::where('date', $date->format('Y-m-d'))
            ->orderBy('time_slot')
            ->get();
        
        // If no slots exist, generate them
        if ($slots->isEmpty()) {
            $slots = $this->generateBookingSlotsForDate($date, $businessHour);
        }
        
        // Filter slots to only include those within current business hours
        $openTime = Carbon::parse($businessHour->open_time);
        $closeTime = Carbon::parse($businessHour->close_time);
        
        $slots = $slots->filter(function($slot) use ($openTime, $closeTime) {
            $slotTime = Carbon::parse($slot->time_slot);
            return $slotTime->greaterThanOrEqualTo($openTime) && $slotTime->lessThan($closeTime);
        });
        
        // Synchronize slot counts with actual bookings
        $this->synchronizeSlotCounts($date->format('Y-m-d'), $slots);
        
        // Refresh slots to get updated values - with business hours filter
        $slots = BookingSlot::where('date', $date->format('Y-m-d'))
            ->whereTime('time_slot', '>=', $businessHour->open_time)
            ->whereTime('time_slot', '<', $businessHour->close_time)
            ->orderBy('time_slot')
            ->get();
        
        return response()->json([
            'success' => true,
            'available' => true,
            'slots' => $slots,
            'businessHours' => $businessHour
        ]);
    }

    /**
     * Generate booking slots for a date based on business hours
     * 
     * @param  \Carbon\Carbon  $date
     * @param  \App\Models\BusinessHour  $businessHour
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function generateBookingSlotsForDate($date, $businessHour)
    {
        $slots = collect();
        
        $openTime = Carbon::parse($businessHour->open_time);
        $closeTime = Carbon::parse($businessHour->close_time);
        
        // Generate slots at 30 min intervals
        $slotTime = clone $openTime;
        
        while ($slotTime < $closeTime) {
            // Check if slot already exists (to prevent duplicates)
            $existingSlot = BookingSlot::where('date', $date->format('Y-m-d'))
                ->where('time_slot', $slotTime->format('H:i:00'))
                ->first();
            
            if (!$existingSlot) {
                $slot = BookingSlot::create([
                    'date' => $date->format('Y-m-d'),
                    'time_slot' => $slotTime->format('H:i:00'),
                    'max_bookings' => 1,
                    'current_bookings' => 0,
                    'is_available' => true
                ]);
                
                $slots->push($slot);
            } else {
                $slots->push($existingSlot);
            }
            
            $slotTime->addMinutes(30);
        }
        
        return $slots;
    }
    
    /**
     * Synchronize slot booking counts with actual bookings
     * 
     * @param  string  $date
     * @param  \Illuminate\Database\Eloquent\Collection  $slots
     * @return void
     */
    private function synchronizeSlotCounts($date, $slots)
    {
        DB::transaction(function () use ($date, $slots) {
            foreach ($slots as $slot) {
                $slotTime = Carbon::parse($date . ' ' . $slot->time_slot);
                $slotEndTime = $slotTime->copy()->addMinutes(30);
                
                // Count actual bookings that overlap this slot
                $actualCount = Booking::where('booking_date', $date)
                    ->whereNotIn('status', ['cancelled', 'no_show'])
                    ->where(function($q) use ($slot, $slotEndTime) {
                        // Booking overlaps if it starts before slot ends and ends after slot starts
                        $q->whereTime('start_time', '<', $slotEndTime->format('H:i:s'))
                          ->whereTime('end_time', '>', $slot->time_slot);
                    })
                    ->count();
                
                // Update slot if count differs
                if ($slot->current_bookings !== $actualCount) {
                    $slot->current_bookings = $actualCount;
                    
                    // Update availability based on bookings
                    if ($slot->max_bookings !== null && $actualCount >= $slot->max_bookings) {
                        $slot->is_available = false;
                    } else {
                        $slot->is_available = true;
                    }
                    
                    $slot->save();
                }
            }
        });
    }

    /**
     * Check if a specific date is available
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkDateAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format',
                'isAvailable' => false
            ], 422);
        }
        
        $date = Carbon::parse($request->date)->format('Y-m-d');
        
        // Check if the date is unavailable
        $unavailableDate = UnavailableDate::where(function($query) use ($date) {
            $query->where('start_date', '<=', $date)
                  ->where('end_date', '>=', $date);
        })->first();
        
        if ($unavailableDate) {
            return response()->json([
                'success' => true,
                'isAvailable' => false,
                'unavailableId' => $unavailableDate->date_id,
                'reason' => $unavailableDate->reason
            ]);
        }
        
        // Check business hours for this day
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $businessHour = BusinessHour::where('day_of_week', $dayOfWeek)->first();
        
        if (!$businessHour || $businessHour->is_closed) {
            return response()->json([
                'success' => true,
                'isAvailable' => false,
                'reason' => 'The studio is closed on this day'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'isAvailable' => true
        ]);
    }
    
    /**
     * Synchronize booking slots for a specific date (manual trigger)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function synchronizeBookingSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $conflictService = app(\App\Services\BookingConflictService::class);
            $result = $conflictService->synchronizeBookingSlotsForDate($request->date);
            
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'slots_updated' => $result['slots_updated']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to synchronize slots: ' . $e->getMessage()
            ], 500);
        }
    }
}
