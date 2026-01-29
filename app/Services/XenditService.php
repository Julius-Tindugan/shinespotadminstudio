<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    protected $secretKey;
    protected $publicKey;
    protected $baseUrl;
    protected $environment;

    public function __construct()
    {
        $this->secretKey = config('services.xendit.secret_key') ?? env('XENDIT_SECRET_KEY');
        $this->publicKey = config('services.xendit.public_key') ?? env('XENDIT_PUBLIC_KEY');
        $this->environment = config('services.xendit.environment') ?? env('XENDIT_ENVIRONMENT', 'production');
        $this->baseUrl = 'https://api.xendit.co';
        
        // Log warnings if using test credentials in production-like environment
        if ($this->secretKey && str_contains($this->secretKey, '_development_')) {
            Log::warning('Xendit: Using DEVELOPMENT credentials. This should only be used for testing!');
        }
        
        // Validate that credentials are properly configured
        if (empty($this->secretKey)) {
            Log::error('Xendit: Secret key is not configured. Please set XENDIT_SECRET_KEY in .env file');
        }
        
        Log::info('Xendit Service initialized', [
            'environment' => $this->environment,
            'has_secret_key' => !empty($this->secretKey),
            'key_type' => $this->secretKey ? (str_contains($this->secretKey, '_production_') ? 'production' : 'development') : 'none',
        ]);
    }

    /**
     * Create E-Wallet payment (GCash, etc.)
     * Using Xendit Payment Request API (new format)
     *
     * @param array $data
     * @return array
     */
    public function createEWalletPayment(array $data)
    {
        try {
            // Validate that secret key is configured
            if (empty($this->secretKey)) {
                Log::error('Xendit secret key not configured');
                return [
                    'success' => false,
                    'message' => 'Payment gateway not properly configured. Please contact administrator.',
                ];
            }

            // Use Payment Method API (v2020-10-31) for GCash
            $payload = [
                'reference_id' => $data['reference_id'],
                'amount' => (float) $data['amount'],
                'currency' => 'PHP',
                'payment_method_id' => null,
                'description' => $data['description'] ?? 'Payment',
                'success_return_url' => url('/payment/callback'),
                'failure_return_url' => url('/payment/callback'),
                'metadata' => [
                    'booking_reference' => $data['reference_id'],
                ],
            ];

            Log::info('Creating Xendit Invoice', [
                'reference_id' => $data['reference_id'],
                'amount' => $data['amount'],
            ]);

            // Use Invoice API which supports GCash
            $invoicePayload = [
                'external_id' => $data['reference_id'],
                'amount' => (float) $data['amount'],
                'payer_email' => $data['customer_email'] ?? 'noreply@shinespot.com',
                'description' => $data['description'] ?? 'Payment for Booking',
                'invoice_duration' => 86400, // 24 hours
                'success_redirect_url' => url('/payment/callback') . '?external_id={external_id}&status={status}&id={id}',
                'failure_redirect_url' => url('/payment/callback') . '?external_id={external_id}&status={status}',
                'payment_methods' => ['GCASH'],
            ];

            $response = Http::withBasicAuth($this->secretKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/v2/invoices', $invoicePayload);

            $responseData = $response->json();

            Log::info('Xendit Invoice API Response', [
                'status' => $response->status(),
                'has_invoice_url' => isset($responseData['invoice_url']),
            ]);

            if ($response->successful() && isset($responseData['id'])) {
                return [
                    'success' => true,
                    'data' => [
                        'id' => $responseData['id'],
                        'reference_id' => $responseData['external_id'],
                        'status' => $responseData['status'] ?? 'PENDING',
                        'actions' => [
                            [
                                'action' => 'AUTH',
                                'url' => $responseData['invoice_url'],
                                'url_type' => 'WEB',
                            ]
                        ],
                        'invoice_url' => $responseData['invoice_url'],
                    ],
                ];
            }

            // Extract error details
            $errorMessage = 'Failed to create payment';
            if (isset($responseData['message'])) {
                $errorMessage = $responseData['message'];
            } elseif (isset($responseData['error_code'])) {
                $errorMessage = $responseData['error_code'];
            }

            Log::error('Xendit Invoice creation failed', [
                'status' => $response->status(),
                'response' => $responseData,
                'payload_sent' => $invoicePayload,
            ]);

            return [
                'success' => false,
                'message' => $errorMessage,
                'error' => $responseData,
            ];

        } catch (\Exception $e) {
            Log::error('Xendit Invoice creation exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while creating payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create invoice for payment
     *
     * @param array $data
     * @return array
     */
    public function createInvoice(array $data)
    {
        try {
            $payload = [
                'external_id' => $data['external_id'],
                'amount' => $data['amount'],
                'payer_email' => $data['payer_email'],
                'description' => $data['description'] ?? 'Invoice',
                'invoice_duration' => $data['invoice_duration'] ?? 86400, // 24 hours
                'customer' => [
                    'given_names' => $data['customer_name'],
                    'email' => $data['payer_email'],
                ],
                'success_redirect_url' => $data['success_redirect_url'] ?? 
                    url('/payment/callback') . '?external_id={external_id}&status={status}&id={id}',
                'failure_redirect_url' => $data['failure_redirect_url'] ?? 
                    url('/payment/callback') . '?external_id={external_id}&status={status}',
            ];

            $response = Http::withBasicAuth($this->secretKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/v2/invoices', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Xendit invoice creation failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to create invoice',
                'error' => $response->json(),
            ];

        } catch (\Exception $e) {
            Log::error('Xendit invoice creation exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while creating invoice: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment status
     *
     * @param string $chargeId
     * @return array
     */
    public function getPaymentStatus(string $chargeId)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get($this->baseUrl . '/ewallets/charges/' . $chargeId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get payment status',
            ];

        } catch (\Exception $e) {
            Log::error('Xendit get payment status exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Get invoice status from Xendit
     *
     * @param string $invoiceId
     * @return array|null
     */
    public function getInvoiceStatus(string $invoiceId)
    {
        try {
            Log::info('Fetching invoice status from Xendit', ['invoice_id' => $invoiceId]);
            
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get($this->baseUrl . '/v2/invoices/' . $invoiceId);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Invoice status retrieved', [
                    'invoice_id' => $invoiceId,
                    'status' => $data['status'] ?? 'unknown'
                ]);
                
                return $data;
            }

            Log::warning('Failed to get invoice status', [
                'invoice_id' => $invoiceId,
                'status_code' => $response->status(),
                'response' => $response->json()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Xendit get invoice status exception', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Verify webhook callback
     *
     * @param string $webhookToken
     * @param string $xCallbackToken
     * @return bool
     */
    public function verifyWebhook(string $xCallbackToken)
    {
        $webhookToken = config('services.xendit.webhook_token') ?? env('XENDIT_WEBHOOK_TOKEN');
        return $webhookToken === $xCallbackToken;
    }
}
