<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status - Shine Spot Photo Studio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <!-- Logo or Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Shine Spot Photo Studio</h1>
                <p class="text-gray-600 mt-2">Payment Status</p>
            </div>

            <!-- Status Icon -->
            <div class="flex justify-center mb-6">
                @if($messageType === 'success')
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                @elseif($messageType === 'warning')
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                @elseif($messageType === 'error')
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                @else
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Message -->
            <div class="text-center mb-8">
                <h2 class="text-xl font-semibold mb-3 @if($messageType === 'success') text-green-600 @elseif($messageType === 'warning') text-yellow-600 @elseif($messageType === 'error') text-red-600 @else text-blue-600 @endif">
                    @if($status === 'PAID' || $status === 'SETTLED')
                        Payment Successful!
                    @elseif($status === 'EXPIRED')
                        Payment Expired
                    @elseif($status === 'FAILED')
                        Payment Failed
                    @else
                        Payment Processing
                    @endif
                </h2>
                <p class="text-gray-700">{{ $message }}</p>
            </div>

            <!-- Additional Information -->
            @if($status === 'PAID' || $status === 'SETTLED')
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">
                        <strong>What's Next?</strong><br>
                        Your payment has been received successfully. Our team will confirm your booking shortly. You will receive a confirmation email with all the details.
                    </p>
                </div>
            @elseif($status === 'EXPIRED')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-yellow-800">
                        The payment link has expired. Please contact Shine Spot Photo Studio to request a new payment link.
                    </p>
                </div>
            @elseif($status === 'FAILED')
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-red-800">
                        The payment could not be processed. Please try again or contact us for assistance.
                    </p>
                </div>
            @endif

            <!-- Contact Information -->
            <div class="border-t pt-6 text-center">
                <p class="text-sm text-gray-600 mb-2">Need assistance?</p>
                <p class="text-sm text-gray-800 font-medium">Contact Shine Spot Photo Studio</p>
                <p class="text-sm text-gray-600">We're here to help!</p>
            </div>

            <!-- Close Window Button -->
            <div class="mt-6 text-center">
                <button onclick="window.close()" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Close Window
                </button>
            </div>
        </div>
    </div>

    <script>
        // Auto-close window after 10 seconds for successful payments
        @if($status === 'PAID' || $status === 'SETTLED')
            setTimeout(function() {
                window.close();
            }, 10000);
        @endif
    </script>
</body>
</html>
