<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use App\Services\SecurityService;

class StaffRegistrationRequest extends FormRequest
{
    protected SecurityService $securityService;
    
    public function __construct(SecurityService $securityService)
    {
        parent::__construct();
        $this->securityService = $securityService;
    }
    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize email
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->email))
            ]);
        }
        
        // Trim whitespace from text fields
        $textFields = ['first_name', 'last_name', 'phone'];
        foreach ($textFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => trim($this->{$field})
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Name validations with security checks
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z\s\-\']+$/', // Only letters, spaces, hyphens, apostrophes
                function ($attribute, $value, $fail) {
                    if ($this->securityService->hasSuspiciousPattern($value)) {
                        $fail('The ' . $attribute . ' contains invalid characters.');
                    }
                },
            ],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z\s\-\']+$/',
                function ($attribute, $value, $fail) {
                    if ($this->securityService->hasSuspiciousPattern($value)) {
                        $fail('The ' . $attribute . ' contains invalid characters.');
                    }
                },
            ],
            
            // Email validation with security checks
            'email' => [
                'required',
                'string',
                'email:rfc,dns', // Validate email format and DNS
                'max:255',
                'unique:staff_users,email',
                function ($attribute, $value, $fail) {
                    $sanitized = $this->securityService->sanitizeEmail($value);
                    if (!$sanitized) {
                        $fail('The ' . $attribute . ' address is invalid or contains suspicious content.');
                    }
                },
            ],
            
            // Phone validation
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/', // Phone number format
                function ($attribute, $value, $fail) {
                    if ($this->securityService->hasSuspiciousPattern($value)) {
                        $fail('The ' . $attribute . ' contains invalid characters.');
                    }
                },
            ],
            
            // Strong password requirements
            'password' => [
                'required',
                'string',
                'min:8',
                'max:128',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(), // Check against known data breaches
                function ($attribute, $value, $fail) {
                    $strengthCheck = $this->securityService->validatePasswordStrength($value);
                    if (!$strengthCheck['valid']) {
                        $fail('Password security issues: ' . implode(', ', $strengthCheck['issues']));
                    }
                },
            ],
            'password_confirmation' => 'required|string|min:8',
            
            // Security fields
            'g-recaptcha-response' => 'required|captcha',
            'timestamp' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!$this->securityService->validateFormTiming($value)) {
                        $fail('Form submission timing is invalid. Please try again.');
                    }
                },
            ],
            'form_token' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!$this->securityService->validateFormToken($value, 'staff_registration')) {
                        $fail('Form security token is invalid. Please refresh the page and try again.');
                    }
                },
            ],
            
            // OTP verification fields (required when OTP is enabled)
            'session_token' => [
                config('otp.enabled', true) ? 'required' : 'nullable',
                'string',
                'size:64',
            ],
            'otp_code' => [
                config('otp.enabled', true) ? 'required' : 'nullable',
                'string',
                'size:' . config('otp.length', 6),
                'regex:/^[0-9]+$/',
            ],
            
            // Honeypot field (should be empty)
            'website' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!$this->securityService->validateHoneypot($value)) {
                        $fail('Invalid form submission detected.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.max' => 'First name must not exceed 50 characters.',
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
            
            'last_name.required' => 'Last name is required.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.max' => 'Last name must not exceed 50 characters.',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
            
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Email address must not exceed 255 characters.',
            
            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'phone.max' => 'Phone number must not exceed 20 digits.',
            'phone.regex' => 'Please enter a valid phone number.',
            
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password must not exceed 128 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.uncompromised' => 'This password has been found in data breaches. Please choose a different password.',
            
            'password_confirmation.required' => 'Password confirmation is required.',
            
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
            'g-recaptcha-response.captcha' => 'reCAPTCHA verification failed. Please try again.',
            
            'timestamp.required' => 'Form timestamp is missing.',
            'timestamp.integer' => 'Invalid form timestamp.',
            
            'form_token.required' => 'Form security token is missing.',
            
            'session_token.required' => 'OTP verification is required. Please verify your email address first.',
            'session_token.size' => 'Invalid OTP session token format.',
            
            'otp_code.required' => 'OTP verification code is required.',
            'otp_code.size' => 'OTP code must be ' . config('otp.length', 6) . ' digits.',
            'otp_code.regex' => 'OTP code must contain only numbers.',
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'email' => 'email address',
            'phone' => 'phone number',
            'password' => 'password',
            'password_confirmation' => 'password confirmation',
            'g-recaptcha-response' => 'reCAPTCHA',
            'timestamp' => 'timestamp',
            'form_token' => 'security token',
            'session_token' => 'OTP session token',
            'otp_code' => 'OTP verification code'
        ];
    }

    /**
     * Handle a failed validation attempt
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Log::warning('Staff registration validation failed', [
            'ip' => $this->ip(),
            'email' => $this->input('email'),
            'errors' => $validator->errors()->toArray()
        ]);
        
        parent::failedValidation($validator);
    }
    
    /**
     * Additional security checks after validation passes
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check for data poisoning
            if ($this->securityService->detectDataPoisoning($this->all())) {
                $validator->errors()->add('security', 'Suspicious data detected. Registration blocked.');
            }
            
            // Validate IP address
            if (!$this->securityService->validateIpAddress($this->ip())) {
                $validator->errors()->add('security', 'Invalid IP address.');
            }
            
            // Validate session integrity
            if (!$this->securityService->validateSessionIntegrity()) {
                $validator->errors()->add('security', 'Session integrity check failed. Please try again.');
            }
        });
    }
}
