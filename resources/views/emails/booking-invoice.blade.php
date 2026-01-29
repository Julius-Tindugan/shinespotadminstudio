<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $booking->booking_reference }}</title>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 650px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #1a1a1a;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .logo-container {
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #1a1a1a;
            text-shadow: 0 2px 4px rgba(255,255,255,0.3);
        }
        .header p {
            margin: 8px 0 0;
            font-size: 16px;
            font-weight: 500;
            opacity: 0.9;
            color: #1a1a1a;
        }
        .content {
            padding: 35px 30px;
        }
        .invoice-details {
            background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%);
            border-left: 4px solid #FFD700;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.15);
        }
        .invoice-details h2 {
            margin: 0 0 20px;
            font-size: 20px;
            color: #FFA500;
            font-weight: 700;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #4a5568;
            font-size: 14px;
        }
        .detail-value {
            color: #1a1a1a;
            font-weight: 600;
            text-align: right;
        }
        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin: 35px 0 20px;
            color: #1a1a1a;
            border-bottom: 3px solid #FFD700;
            padding-bottom: 10px;
            display: inline-block;
            width: 100%;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
            align-items: center;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .item-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 15px;
        }
        .item-qty {
            color: #718096;
            font-size: 13px;
            margin-top: 4px;
        }
        .item-price {
            font-weight: 700;
            color: #FFA500;
            font-size: 16px;
        }
        .totals {
            background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%);
            padding: 25px;
            margin-top: 30px;
            border-radius: 10px;
            border: 2px solid #FFD700;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 600;
        }
        .total-row.grand-total {
            border-top: 3px solid #FFD700;
            margin-top: 15px;
            padding-top: 18px;
            font-size: 22px;
            font-weight: 800;
            color: #1a1a1a;
        }
        .total-row.grand-total span:last-child {
            color: #FFA500;
        }
        .payment-status {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-paid {
            background-color: #c6f6d5;
            color: #22543d;
            border: 2px solid #48bb78;
        }
        .status-unpaid {
            background-color: #fed7d7;
            color: #742a2a;
            border: 2px solid #fc8181;
        }
        .status-partial {
            background-color: #feebc8;
            color: #7c2d12;
            border: 2px solid #ed8936;
        }
        .footer {
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #CCCCCC;
        }
        .footer p {
            margin: 8px 0;
        }
        .footer strong {
            color: #FFD700;
            font-size: 16px;
        }
        .highlight {
            background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%);
            padding: 20px;
            border-left: 5px solid #FFD700;
            margin: 25px 0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.2);
        }
        .highlight strong {
            color: #FFA500;
            font-size: 16px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-confirmed {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-completed {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 8px;
            }
            .content {
                padding: 25px 20px;
            }
            .header {
                padding: 30px 20px;
            }
            .header h1 {
                font-size: 26px;
            }
            .totals {
                padding: 20px 15px;
            }
            .item-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .item-price {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-container">
                <svg width="60" height="60" viewBox="0 0 1024 1024" style="display: inline-block;">
                    <path fill="#1a1a1a" opacity="0.9" d="M512 102.4c226.2 0 409.6 183.4 409.6 409.6S738.2 921.6 512 921.6 102.4 738.2 102.4 512 285.8 102.4 512 102.4z"/>
                    <circle cx="512" cy="512" r="150" fill="#ffffff" opacity="0.3"/>
                    <path fill="#1a1a1a" d="M512 400c-61.9 0-112 50.1-112 112s50.1 112 112 112 112-50.1 112-112-50.1-112-112-112zm0 184c-39.7 0-72-32.3-72-72s32.3-72 72-72 72 32.3 72 72-32.3 72-72 72z"/>
                </svg>
            </div>
            <h1>INVOICE</h1>
            <p>Shine Spot Studio</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Booking Details -->
            <div class="invoice-details">
                <h2>📋 Booking Information</h2>
                <div class="detail-row">
                    <span class="detail-label">Reference Number:</span>
                    <span class="detail-value" style="font-family: 'Courier New', monospace; font-weight: 700; font-size: 15px; color: #FFA500;"><strong>{{ $booking->booking_reference }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">👤 Client Name:</span>
                    <span class="detail-value">{{ $booking->client_first_name }} {{ $booking->client_last_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">📧 Email:</span>
                    <span class="detail-value">{{ $booking->client_email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">📱 Phone:</span>
                    <span class="detail-value">{{ $booking->client_phone }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">📅 Booking Date:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">🕐 Time:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        @php
                            $statusClass = 'badge-pending';
                            if($booking->status === 'confirmed') $statusClass = 'badge-confirmed';
                            elseif($booking->status === 'completed') $statusClass = 'badge-completed';
                            elseif($booking->status === 'cancelled') $statusClass = 'badge-cancelled';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                    </span>
                </div>
            </div>

            <!-- Package Details -->
            @if($booking->package)
            <h3 class="section-title">📦 Package Details</h3>
            <div class="item-row">
                <div>
                    <div class="item-name">{{ $booking->package->title }}</div>
                    @if($booking->package->short_description)
                        <div class="item-qty">{{ $booking->package->short_description }}</div>
                    @endif
                </div>
                <div class="item-price">₱{{ number_format($booking->package->price, 2) }}</div>
            </div>
            @endif

            <!-- Addons -->
            @if($booking->addons && (is_countable($booking->addons) ? count($booking->addons) : 0) > 0)
                <h3 class="section-title">🎁 Add-ons</h3>
                @foreach($booking->addons as $addon)
                    @php
                        // Handle both array and object formats
                        $addonName = is_array($addon) ? ($addon['addon_name'] ?? 'Unknown') : ($addon->addon_name ?? 'Unknown');
                        $quantity = is_array($addon) ? ($addon['pivot']['quantity'] ?? 1) : ($addon->pivot->quantity ?? 1);
                        $price = is_array($addon) ? ($addon['pivot']['price'] ?? 0) : ($addon->pivot->price ?? 0);
                    @endphp
                    <div class="item-row">
                        <div>
                            <div class="item-name">{{ $addonName }}</div>
                            <div class="item-qty">Quantity: {{ $quantity }}</div>
                        </div>
                        <div class="item-price">₱{{ number_format($price * $quantity, 2) }}</div>
                    </div>
                @endforeach
            @endif

            <!-- Totals -->
            <h3 class="section-title">💵 Payment Summary</h3>
            <div class="totals">
                @if($booking->package)
                <div class="total-row">
                    <span>Package:</span>
                    <span>₱{{ number_format($booking->package->price, 2) }}</span>
                </div>
                @endif
                @if($addonsTotal > 0)
                    <div class="total-row">
                        <span>Add-ons:</span>
                        <span>₱{{ number_format($addonsTotal, 2) }}</span>
                    </div>
                @endif
                <div class="total-row grand-total">
                    <span>Total Amount:</span>
                    <span>₱{{ number_format($booking->total_amount, 2) }}</span>
                </div>
                @if($totalPaid > 0)
                    <div class="total-row" style="color: #22543d; font-weight: 600;">
                        <span>💰 Amount Paid:</span>
                        <span style="color: #48bb78;">₱{{ number_format($totalPaid, 2) }}</span>
                    </div>
                @endif
                @if($balance > 0)
                    <div class="total-row" style="color: #742a2a; font-weight: 600;">
                        <span>⚠ Outstanding Balance:</span>
                        <span style="color: #fc8181;">₱{{ number_format($balance, 2) }}</span>
                    </div>
                @elseif($balance == 0 && $totalPaid > 0)
                    <div class="total-row" style="color: #22543d; font-weight: 600; background: #c6f6d5; padding: 10px; margin: 10px -25px -5px -25px; border-radius: 0 0 8px 8px;">
                        <span>✓ Paid in Full</span>
                        <span>Thank You!</span>
                    </div>
                @endif
            </div>

            <!-- Payment Status -->
            <div style="margin-top: 30px; text-align: center; padding: 25px; background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%); border-radius: 8px; border: 2px solid #FFD700;">
                <p style="margin-bottom: 15px; font-weight: 700; color: #1a1a1a; font-size: 16px;">Payment Status</p>
                @if($balance <= 0)
                    <span class="payment-status status-paid">✓ Fully Paid</span>
                @elseif($totalPaid > 0)
                    <span class="payment-status status-partial">⚠ Partially Paid</span>
                @else
                    <span class="payment-status status-unpaid">⏳ Awaiting Payment</span>
                @endif
            </div>

            @if($balance > 0)
                <div class="highlight">
                    <strong>⚠ Payment Reminder:</strong> You have an outstanding balance of <strong>₱{{ number_format($balance, 2) }}</strong>. 
                    Please settle this before your booking date to ensure a smooth experience.
                </div>
            @endif

            <!-- Payment History -->
            @if($booking->payments->count() > 0)
                <h3 class="section-title">💳 Payment History</h3>
                <div style="background-color: #f7fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    @foreach($booking->payments as $payment)
                        <div class="item-row" style="border-color: #cbd5e0;">
                            <div>
                                <div class="item-name" style="color: #2d3748;">{{ $payment->formatted_payment_method }}</div>
                                <div class="item-qty" style="color: #718096;">
                                    📅 {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y • g:i A') }}
                                </div>
                            </div>
                            <div class="item-price" style="color: #48bb78;">₱{{ number_format($payment->amount, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($booking->notes)
                <h3 class="section-title">📝 Additional Notes</h3>
                <p style="padding: 15px; background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%); border-radius: 8px; border-left: 3px solid #FFD700; color: #1a1a1a; line-height: 1.7;">{{ $booking->notes }}</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>✨ Shine Spot Studio ✨</strong></p>
            <p style="margin-top: 15px; font-size: 14px;">Thank you for choosing our services!</p>
            <p style="color: #a0aec0;">📞 For inquiries, please contact us at your convenience.</p>
            <p style="margin-top: 20px; font-size: 11px; color: #718096; border-top: 1px solid #4a5568; padding-top: 15px;">
                This is an automated email. Please do not reply to this message.<br>
                © {{ date('Y') }} Shine Spot Studio. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
