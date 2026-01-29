<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\OTPService;
use App\Services\NotificationService;
use App\Services\BookingReferenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PublicBookingController extends Controller
{
    /**
     * The OTP service instance.
     */
    protected $otpService;

    /**
     * The notification service instance.
     */
    protected $notificationService;
    
    /**
     * The booking reference service instance.
     */
    protected $referenceService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\OTPService  $otpService
     * @param  \App\Services\NotificationService  $notificationService
     * @param  \App\Services\BookingReferenceService  $referenceService
     * @return void
     */
    public function __construct(
        OTPService $otpService, 
        NotificationService $notificationService,
        BookingReferenceService $referenceService
    )
    {
        $this->otpService = $otpService;
        $this->notificationService = $notificationService;
        $this->referenceService = $referenceService;
    }

    /**
     * Show the booking lookup form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLookupForm()
    {
        return view('public.bookings.lookup');
    }
    
    /**
     * Show the manage booking page after verification
     *
     * @param  string  $reference
     * @return \Illuminate\Http\Response
     */
    public function showManageBooking($reference)
    {
        $booking = Booking::with(['package', 'addons', 'backdrop', 'primaryStaff'])
            ->where('booking_reference', $reference)
            ->first();
        
        if (!$booking) {
            return redirect()->route('public.bookings.lookup')
                ->withErrors(['booking_not_found' => 'Booking not found.']);
        }
        
        // If booking is already canceled, show canceled view
        if ($booking->status === 'canceled') {
            return view('public.bookings.canceled', compact('booking'));
        }
        
        // For past bookings, limit management options
        $isPastBooking = Carbon::parse($booking->booking_date)->isBefore(Carbon::today());
        
        return view('public.bookings.manage', compact('booking', 'isPastBooking'));
    }

    /**
     * Find booking by reference code and email/phone
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function findBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_reference' => 'required|string',
            'client_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide both booking reference and email address.',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::where('booking_reference', $request->booking_reference)
                         ->where('client_email', $request->client_email)
                         ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'No booking found with the provided reference code and email address.',
            ], 404);
        }

        // Check if booking is in a valid state for management
        if (in_array($booking->status, ['completed', 'canceled'])) {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be modified as it has been ' . $booking->status . '.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking found successfully.',
            'booking' => [
                'id' => $booking->booking_id,
                'reference' => $booking->booking_reference,
                'client_name' => $booking->client_first_name . ' ' . $booking->client_last_name,
                'date' => $booking->booking_date->format('M d, Y'),
                'time' => $booking->start_time->format('h:i A') . ' - ' . $booking->end_time->format('h:i A'),
                'status' => $booking->status,
                'package' => $booking->package ? $booking->package->title : 'No package',
                'amount' => $booking->total_amount,
            ]
        ]);
    }

    /**
     * Send OTP for booking verification
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_reference' => 'required|string',
            'client_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide both booking reference and email address.',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::where('booking_reference', $request->booking_reference)
                         ->where('client_email', $request->client_email)
                         ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'No booking found with the provided reference code and email address.',
            ], 404);
        }

        $result = $this->otpService->generateAndSendOTP($booking);

        return response()->json($result);
    }

    /**
     * Verify OTP and show booking management options
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_reference' => 'required|string',
            'client_email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide booking reference, email, and OTP.',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::where('booking_reference', $request->booking_reference)
                         ->where('client_email', $request->client_email)
                         ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'No booking found with the provided reference code and email address.',
            ], 404);
        }

        $result = $this->otpService->verifyOTP($booking, $request->otp);

        if (!$result['success']) {
            return response()->json($result);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully. You can now manage your booking.',
            'booking' => [
                'id' => $booking->booking_id,
                'reference' => $booking->booking_reference,
                'client_name' => $booking->client_first_name . ' ' . $booking->client_last_name,
                'date' => $booking->booking_date->format('M d, Y'),
                'time' => $booking->start_time->format('h:i A') . ' - ' . $booking->end_time->format('h:i A'),
                'status' => $booking->status,
                'package' => $booking->package ? $booking->package->title : 'No package',
                'amount' => $booking->total_amount,
            ]
        ]);
    }

    /**
     * Cancel a booking
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancelBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_reference' => 'required|string',
            'client_email' => 'required|email',
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide booking reference and email.',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::where('booking_reference', $request->booking_reference)
                         ->where('client_email', $request->client_email)
                         ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'No booking found with the provided reference code and email address.',
            ], 404);
        }

        // Check if booking can be cancelled
        if (in_array($booking->status, ['completed', 'canceled'])) {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be cancelled as it has been ' . $booking->status . '.',
            ], 400);
        }

        // Check if booking is too close to the date (e.g., less than 24 hours)
        $hoursUntilBooking = Carbon::now()->diffInHours($booking->booking_date . ' ' . $booking->start_time, false);
        if ($hoursUntilBooking < 24 && $hoursUntilBooking > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bookings cannot be cancelled less than 24 hours before the scheduled time.',
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Store old values for logging
            $oldValues = $booking->toArray();

            // Update booking status
            $booking->status = 'canceled';
            $booking->canceled_at = now();

            // Add cancellation reason to notes
            $cancellationNote = $request->cancellation_reason ? "\n\nCancellation Reason: " . $request->cancellation_reason : '';
            $booking->notes = ($booking->notes ? $booking->notes : '') . $cancellationNote;

            $booking->save();

            // Log the cancellation
            \App\Models\BookingLog::log($booking, 'cancelled', $oldValues, $booking->toArray(), $request->ip(), $request->userAgent());

            // Send cancellation notification
            $this->notificationService->sendBookingCancellation($booking);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Your booking has been cancelled successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error cancelling booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show reschedule options for a booking
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showRescheduleOptions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_reference' => 'required|string',
            'client_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide booking reference and email.',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::where('booking_reference', $request->booking_reference)
                         ->where('client_email', $request->client_email)
                         ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'No booking found with the provided reference code and email address.',
            ], 404);
        }

        // Check if booking can be rescheduled
        if (in_array($booking->status, ['completed', 'canceled'])) {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be rescheduled as it has been ' . $booking->status . '.',
            ], 400);
        }

        // Get available dates (next 30 days)
        $availableDates = $this->getAvailableDates(30);

        return response()->json([
            'success' => true,
            'message' => 'Reschedule options loaded successfully.',
            'booking' => [
                'id' => $booking->booking_id,
                'reference' => $booking->booking_reference,
                'current_date' => $booking->booking_date->format('Y-m-d'),
                'current_time' => $booking->start_time->format('H:i'),
                'duration' => $booking->start_time->diffInMinutes($booking->end_time),
            ],
            'available_dates' => $availableDates,
        ]);
    }

    /**
     * Reschedule a booking
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rescheduleBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_reference' => 'required|string',
            'client_email' => 'required|email',
            'new_date' => 'required|date|after:today',
            'new_time' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide all required fields.',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::where('booking_reference', $request->booking_reference)
                         ->where('client_email', $request->client_email)
                         ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'No booking found with the provided reference code and email address.',
            ], 404);
        }

        // Check if booking can be rescheduled
        if (in_array($booking->status, ['completed', 'canceled'])) {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be rescheduled as it has been ' . $booking->status . '.',
            ], 400);
        }

        // Check if new date/time is available
        $isAvailable = $this->checkAvailability($request->new_date, $request->new_time, $booking->end_time->diffInMinutes($booking->start_time));

        if (!$isAvailable) {
            return response()->json([
                'success' => false,
                'message' => 'The selected date and time is not available. Please choose a different time.',
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Store old values for logging
            $oldValues = $booking->toArray();

            // Update booking
            $booking->booking_date = $request->new_date;
            $booking->start_time = $request->new_time;
            $booking->end_time = Carbon::createFromFormat('H:i', $request->new_time)
                                     ->addMinutes($booking->start_time->diffInMinutes($booking->end_time))
                                     ->format('H:i:s');

            $booking->save();

            // Log the reschedule
            \App\Models\BookingLog::log($booking, 'rescheduled', $oldValues, $booking->toArray(), $request->ip(), $request->userAgent());

            // Send reschedule notification
            $this->notificationService->sendBookingReschedule($booking, $oldValues);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Your booking has been rescheduled successfully.',
                'booking' => [
                    'reference' => $booking->booking_reference,
                    'new_date' => $booking->booking_date->format('M d, Y'),
                    'new_time' => $booking->start_time->format('h:i A') . ' - ' . $booking->end_time->format('h:i A'),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error rescheduling booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available dates for rescheduling
     *
     * @param int $days
     * @return array
     */
    private function getAvailableDates($days = 30)
    {
        $dates = [];
        $startDate = Carbon::tomorrow();

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);

            // Skip unavailable dates (you can implement business logic here)
            // For now, we'll assume all dates are available
            $dates[] = [
                'date' => $date->format('Y-m-d'),
                'formatted' => $date->format('M d, Y'),
                'day_name' => $date->format('l'),
                'available' => true,
            ];
        }

        return $dates;
    }

    /**
     * Check if a date/time slot is available
     *
     * @param string $date
     * @param string $startTime
     * @param int $duration
     * @return bool
     */
    private function checkAvailability($date, $startTime, $duration)
    {
        // This is a placeholder - implement actual availability checking logic
        // You would check against existing bookings, unavailable dates, business hours, etc.

        return true; // For now, assume all slots are available
    }
}
