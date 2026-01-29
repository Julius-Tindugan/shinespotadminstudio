<?php

namespace App\Rules;

use App\Services\RecaptchaEnterpriseService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class RecaptchaEnterprise implements ValidationRule
{
    private string $action;

    public function __construct(string $action = 'submit')
    {
        $this->action = $action;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if reCAPTCHA Enterprise is enabled
        if (!config('captcha.enterprise.enabled', false)) {
            // Fall back to standard reCAPTCHA validation
            $validator = app('validator');
            $rules = ['g-recaptcha-response' => 'required|captcha'];
            
            $validation = $validator->make(['g-recaptcha-response' => $value], $rules);
            
            if ($validation->fails()) {
                $fail('Please complete the security verification (reCAPTCHA).');
            }
            return;
        }

        // Use reCAPTCHA Enterprise validation
        if (empty($value)) {
            $fail('Please complete the security verification (reCAPTCHA).');
            return;
        }

        try {
            $recaptchaService = new RecaptchaEnterpriseService();
            $result = $recaptchaService->validateToken($value, $this->action);

            if (!$result['success']) {
                Log::warning('reCAPTCHA Enterprise validation failed', [
                    'message' => $result['message'],
                    'action' => $this->action
                ]);

                $fail('Security verification failed. Please try again.');
                return;
            }

            Log::info('reCAPTCHA Enterprise validation successful', [
                'score' => $result['score'] ?? 'unknown',
                'action' => $this->action
            ]);

        } catch (\Exception $e) {
            Log::error('reCAPTCHA Enterprise validation exception', [
                'error' => $e->getMessage(),
                'action' => $this->action
            ]);

            $fail('Security verification encountered an error. Please try again.');
        }
    }
}