<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaV3 implements ValidationRule
{
    private string $action;
    private float $minimumScore;

    public function __construct(string $action = 'submit', float $minimumScore = 0.5)
    {
        $this->action = $action;
        $this->minimumScore = $minimumScore;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('Please complete the security verification (reCAPTCHA).');
            return;
        }

        $secretKey = config('recaptcha.secret_key');
        
        if (empty($secretKey)) {
            Log::error('reCAPTCHA v3 secret key not configured');
            $fail('reCAPTCHA configuration error.');
            return;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            if (!$result['success']) {
                Log::warning('reCAPTCHA v3 validation failed', [
                    'errors' => $result['error-codes'] ?? [],
                    'action' => $this->action
                ]);
                $fail('Security verification failed. Please try again.');
                return;
            }

            // Check action matches
            if (isset($result['action']) && $result['action'] !== $this->action) {
                Log::warning('reCAPTCHA v3 action mismatch', [
                    'expected' => $this->action,
                    'received' => $result['action']
                ]);
                $fail('Security verification failed. Please try again.');
                return;
            }

            // Check score
            $score = $result['score'] ?? 0;
            if ($score < $this->minimumScore) {
                Log::warning('reCAPTCHA v3 score too low', [
                    'score' => $score,
                    'minimum' => $this->minimumScore,
                    'action' => $this->action
                ]);
                $fail('Security verification failed. Please try again.');
                return;
            }

            Log::info('reCAPTCHA v3 validation successful', [
                'score' => $score,
                'action' => $this->action
            ]);

        } catch (\Exception $e) {
            Log::error('reCAPTCHA v3 validation exception', [
                'error' => $e->getMessage(),
                'action' => $this->action
            ]);
            $fail('Security verification encountered an error. Please try again.');
        }
    }
}