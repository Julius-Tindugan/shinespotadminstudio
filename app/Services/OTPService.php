<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OTPService
{
    /**
     * Generate and send OTP for booking verification
     *
     * @param Booking $booking
     * @return array
     */
    public function generateAndSendOTP(Booking $booking)
    {
        // Generate 6-digit OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Set expiry time (10 minutes from now)
        $expiresAt = Carbon::now()->addMinutes(10);

        // Update booking with OTP
        $booking->update([
            'otp_code' => $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        // Send OTP via email
        $this->sendOTPEmail($booking, $otp);

        // Send OTP via SMS (if phone number exists)
        if ($booking->client_phone) {
            $this->sendOTPSMS($booking, $otp);
        }

        return [
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => $otp, // Only for development - remove in production
        ];
    }

    /**
     * Verify OTP for booking
     *
     * @param Booking $booking
     * @param string $otp
     * @return array
     */
    public function verifyOTP(Booking $booking, string $otp)
    {
        // Check if OTP exists and hasn't expired
        if (!$booking->otp_code || !$booking->otp_expires_at) {
            return [
                'success' => false,
                'message' => 'No OTP found for this booking',
            ];
        }

        // Check if OTP has expired
        if (Carbon::now()->isAfter($booking->otp_expires_at)) {
            return [
                'success' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ];
        }

        // Verify OTP
        if ($booking->otp_code !== $otp) {
            return [
                'success' => false,
                'message' => 'Invalid OTP. Please try again.',
            ];
        }

        // Clear OTP after successful verification
        $booking->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return [
            'success' => true,
            'message' => 'OTP verified successfully',
        ];
    }

    /**
     * Send OTP via email
     *
     * @param Booking $booking
     * @param string $otp
     */
    private function sendOTPEmail(Booking $booking, string $otp)
    {
        try {
            $notificationService = app(NotificationService::class);
            $notificationService->sendOTPEmail($booking, $otp);
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
        }
    }

    /**
     * Send OTP via SMS
     *
     * @param Booking $booking
     * @param string $otp
     */
    private function sendOTPSMS(Booking $booking, string $otp)
    {
        try {
            $notificationService = app(NotificationService::class);
            $notificationService->sendOTPSMS($booking, $otp);
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP SMS: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique booking reference code
     *
     * @return string
     */
    public function generateBookingReference()
    {
        do {
            // Generate code like BK-8921-XY4
            $numbers = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
            $letters = strtoupper(Str::random(3));
            $reference = "SPS-{$numbers}-{$letters}";
        } while (Booking::where('booking_reference', $reference)->exists());

        return $reference;
    }
}
