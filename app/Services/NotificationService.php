<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send booking confirmation notification
     *
     * @param Booking $booking
     * @return array
     */
    public function sendBookingConfirmation(Booking $booking)
    {
        try {
            $emailData = [
                'booking' => $booking,
                'type' => 'confirmation',
            ];

            Mail::send('emails.booking_confirmation', $emailData, function ($message) use ($booking) {
                $message->to($booking->client_email)
                        ->subject('Booking Confirmation - ' . $booking->booking_reference);
            });

            // Send SMS if phone number exists
            if ($booking->client_phone) {
                $this->sendBookingConfirmationSMS($booking);
            }

            return [
                'success' => true,
                'message' => 'Confirmation sent successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send confirmation: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Send booking rescheduled notification
     * 
     * @param Booking $booking
     * @param array $oldValues
     * @return array
     */
    public function sendBookingRescheduledNotification(Booking $booking, $oldValues = null)
    {
        try {
            $emailData = [
                'booking' => $booking,
                'oldValues' => $oldValues,
                'type' => 'rescheduled',
            ];

            Mail::send('emails.booking_rescheduled', $emailData, function ($message) use ($booking) {
                $message->to($booking->client_email)
                        ->subject('Booking Rescheduled - ' . $booking->booking_reference);
            });

            // Send SMS if phone number exists
            if ($booking->client_phone) {
                $this->sendBookingRescheduledSMS($booking);
            }

            return [
                'success' => true,
                'message' => 'Rescheduling notification sent successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send booking rescheduled notification: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send rescheduling notification: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Send booking cancellation notification
     * 
     * @param Booking $booking
     * @return array
     */
    public function sendBookingCancellationNotification(Booking $booking)
    {
        try {
            $emailData = [
                'booking' => $booking,
                'type' => 'cancellation',
            ];

            Mail::send('emails.booking_cancellation', $emailData, function ($message) use ($booking) {
                $message->to($booking->client_email)
                        ->subject('Booking Cancelled - ' . $booking->booking_reference);
            });

            // Send SMS if phone number exists
            if ($booking->client_phone) {
                $this->sendBookingCancellationSMS($booking);
            }

            return [
                'success' => true,
                'message' => 'Cancellation notification sent successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send booking cancellation notification: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send cancellation notification: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Send OTP email for booking verification
     * 
     * @param Booking $booking
     * @param string $otp
     * @return array
     */
    public function sendOTPEmail(Booking $booking, $otp)
    {
        try {
            $emailData = [
                'booking' => $booking,
                'otp' => $otp,
            ];

            Mail::send('emails.booking_otp', $emailData, function ($message) use ($booking) {
                $message->to($booking->client_email)
                        ->subject('Verification Code for Your Booking - ' . $booking->booking_reference);
            });

            return [
                'success' => true,
                'message' => 'OTP sent successfully via email',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send OTP email: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Send OTP SMS for booking verification
     * 
     * @param Booking $booking
     * @param string $otp
     * @return array
     */
    public function sendOTPSMS(Booking $booking, $otp)
    {
        try {
            // SMS implementation goes here
            // For now, we'll just log it
            Log::info("SMS would be sent to {$booking->client_phone} with OTP: {$otp}");
            
            return [
                'success' => true,
                'message' => 'OTP sent successfully via SMS',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send OTP SMS: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send OTP SMS: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Send booking confirmation SMS
     *
     * @param Booking $booking
     * @return array
     */
    public function sendBookingConfirmationSMS(Booking $booking)
    {
        try {
            // SMS implementation goes here
            // For now, we'll just log it
            $message = "Your booking has been confirmed. Reference: {$booking->booking_reference}. " .
                      "To manage your booking, visit: " . route('public.bookings.lookup');
                      
            Log::info("SMS would be sent to {$booking->client_phone}: {$message}");
            
            return [
                'success' => true,
                'message' => 'SMS confirmation sent successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation SMS: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send SMS confirmation: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Send booking rescheduled SMS
     *
     * @param Booking $booking
     * @return array
     */
    public function sendBookingRescheduledSMS(Booking $booking)
    {
        try {
            // SMS implementation goes here
            // For now, we'll just log it
            $message = "Your booking has been rescheduled. Reference: {$booking->booking_reference}. " .
                      "New date: " . $booking->formatted_date_time . ". " .
                      "To manage your booking, visit: " . route('public.bookings.lookup');
                      
            Log::info("SMS would be sent to {$booking->client_phone}: {$message}");
            
            return [
                'success' => true,
                'message' => 'SMS rescheduling notification sent successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send booking rescheduled SMS: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send SMS rescheduling notification: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Send booking cancellation SMS
     *
     * @param Booking $booking
     * @return array
     */
    public function sendBookingCancellationSMS(Booking $booking)
    {
        try {
            // SMS implementation goes here
            // For now, we'll just log it
            $message = "Your booking has been cancelled. Reference: {$booking->booking_reference}. " .
                      "If you need to book again, visit: " . route('public.bookings.lookup');
                      
            Log::info("SMS would be sent to {$booking->client_phone}: {$message}");
            
            return [
                'success' => true,
                'message' => 'SMS cancellation notification sent successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send booking cancellation SMS: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send SMS cancellation notification: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send booking cancellation notification
     *
     * @param Booking $booking
     * @return array
     */
    public function sendBookingCancellation(Booking $booking)
    {
        try {
            $emailData = [
                'booking' => $booking,
                'type' => 'cancellation',
            ];

            Mail::send('emails.booking_cancellation', $emailData, function ($message) use ($booking) {
                $message->to($booking->client_email)
                        ->subject('Booking Cancelled - ' . $booking->booking_reference);
            });

            // Send SMS if phone number exists
            if ($booking->client_phone) {
                $this->sendBookingCancellationSMS($booking);
            }

            return [
                'success' => true,
                'message' => 'Cancellation notification sent successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send booking cancellation: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send cancellation: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send booking reschedule notification
     *
     * @param Booking $booking
     * @param array $oldBookingData
     * @return array
     */
    public function sendBookingReschedule(Booking $booking, array $oldBookingData)
    {
        try {
            $emailData = [
                'booking' => $booking,
                'oldBookingData' => $oldBookingData,
                'type' => 'reschedule',
            ];

            Mail::send('emails.booking_reschedule', $emailData, function ($message) use ($booking) {
                $message->to($booking->client_email)
                        ->subject('Booking Rescheduled - ' . $booking->booking_reference);
            });

            // Send SMS if phone number exists
            if ($booking->client_phone) {
                $this->sendBookingRescheduleSMS($booking, $oldBookingData);
            }

            return [
                'success' => true,
                'message' => 'Reschedule notification sent successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send booking reschedule: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send reschedule: ' . $e->getMessage(),
            ];
        }
    }

    // Removed duplicate method

    // Removed duplicate method

    // Removed duplicate method

    /**
     * Send reminder notification
     *
     * @param Booking $booking
     * @param int $hoursBefore
     * @return array
     */
    public function sendBookingReminder(Booking $booking, int $hoursBefore = 24)
    {
        try {
            $emailData = [
                'booking' => $booking,
                'hoursBefore' => $hoursBefore,
                'type' => 'reminder',
            ];

            Mail::send('emails.booking_reminder', $emailData, function ($message) use ($booking) {
                $message->to($booking->client_email)
                        ->subject('Booking Reminder - ' . $booking->booking_reference);
            });

            return [
                'success' => true,
                'message' => 'Reminder sent successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send booking reminder: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send reminder: ' . $e->getMessage(),
            ];
        }
    }
}
