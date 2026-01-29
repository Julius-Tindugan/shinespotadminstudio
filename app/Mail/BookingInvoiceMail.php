<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $totalPaid;
    public $balance;
    public $addonsTotal;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        
        // Ensure relationships are loaded
        if (!$booking->relationLoaded('payments')) {
            $booking->load('payments');
        }
        if (!$booking->relationLoaded('addons')) {
            $booking->load('addons');
        }
        
        // Only count successful payments (PAID/SETTLED or onsite payments)
        $payments = $booking->payments ?? collect([]);
        $this->totalPaid = $payments
            ->filter(function($payment) {
                return in_array($payment->xendit_status, ['PAID', 'SETTLED']) || 
                       in_array($payment->payment_method, ['onsite_cash', 'onsite_card']);
            })
            ->sum('amount');
            
        $this->balance = $booking->total_amount - $this->totalPaid;
        
        // Calculate addons total - safely handle null, empty, or array
        $this->addonsTotal = 0;
        $addons = $booking->addons;
        
        // Convert to collection if it's an array
        if (is_array($addons)) {
            $addons = collect($addons);
        } elseif ($addons === null) {
            $addons = collect([]);
        }
        
        if ($addons->isNotEmpty()) {
            foreach ($addons as $addon) {
                if (is_object($addon) && isset($addon->pivot->price) && isset($addon->pivot->quantity)) {
                    $this->addonsTotal += $addon->pivot->price * $addon->pivot->quantity;
                }
            }
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Invoice for Booking ' . $this->booking->booking_reference)
                    ->view('emails.booking-invoice')
                    ->with([
                        'booking' => $this->booking,
                        'totalPaid' => $this->totalPaid,
                        'balance' => $this->balance,
                        'addonsTotal' => $this->addonsTotal,
                    ]);
    }
}
