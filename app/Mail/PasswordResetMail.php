<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $userType;
    public $resetUrl;
    public $expirationTime;
    public $ipAddress;
    public $requestTime;
    public $userAgent;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $userName,
        string $userType,
        string $resetUrl,
        string $expirationTime = '1 hour',
        string $ipAddress = 'Unknown',
        string $userAgent = 'Unknown'
    ) {
        $this->userName = $userName;
        $this->userType = $userType;
        $this->resetUrl = $resetUrl;
        $this->expirationTime = $expirationTime;
        $this->ipAddress = $ipAddress;
        $this->requestTime = now()->format('M j, Y g:i A T');
        $this->userAgent = $userAgent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Shine Spot Password - Security Alert',
            replyTo: config('mail.from.address')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.password-reset',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
