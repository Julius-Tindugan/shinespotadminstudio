<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use App\Services\SecurityService;

class AdminRegistrationRequest extends FormRequest
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
        $textFields = ['username', 'first_name', 'last_name', 'phone'];
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
            'username' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[a-zA-Z0-9_]{3,20}$/',
                'unique:admin_users,username',
                function ($attribute, $value, $fail) {
                    if ($this->securityService->hasSuspiciousPattern($value)) {
                        $fail('The ' . $attribute . ' contains invalid characters.');
                    }
                },
            ],
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z\s\-\']{2,50}$/',
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
                'regex:/^[a-zA-Z\s\-\']{2,50}$/',
                function ($attribute, $value, $fail) {
                    if ($this->securityService->hasSuspiciousPattern($value)) {
                        $fail('The ' . $attribute . ' contains invalid characters.');
                    }
                },
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:admin_users,email',
                function ($attribute, $value, $fail) {
                    $sanitized = $this->securityService->sanitizeEmail($value);
                    if (!$sanitized) {
                        $fail('The ' . $attribute . ' address is invalid or contains suspicious content.');
                    }
                },
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[\+]?[0-9\s\-\(\)]{10,15}$/',
                function ($attribute, $value, $fail) {
                    if ($this->securityService->hasSuspiciousPattern($value)) {
                        $fail('The ' . $attribute . ' contains invalid characters.');
                    }
                },
            ],
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
                    ->uncompromised(),
                function ($attribute, $value, $fail) {
                    $strengthCheck = $this->securityService->validatePasswordStrength($value);
                    if (!$strengthCheck['valid']) {
                        $fail('Password security issues: ' . implode(', ', $strengthCheck['issues']));
                    }
                },
            ],
            'password_confirmation' => 'required|string|min:8',
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
                    if (!$this->securityService->validateFormToken($value, 'admin_registration')) {
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
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 3 characters long.',
            'username.max' => 'Username must not exceed 20 characters.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'username.unique' => 'This username is already taken.',
            
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name must be at least 2 characters long.',
            'first_name.max' => 'First name must not exceed 50 characters.',
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
            
            'last_name.required' => 'Last name is required.',
            'last_name.min' => 'Last name must be at least 2 characters long.',
            'last_name.max' => 'Last name must not exceed 50 characters.',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
            
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Email address must not exceed 255 characters.',
            
            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'phone.max' => 'Phone number must not exceed 15 digits.',
            'phone.regex' => 'Please enter a valid phone number.',
            
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.max' => 'Password must not exceed 128 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.uncompromised' => 'This password has been found in data breaches. Please choose a different password.',
            
            'password_confirmation.required' => 'Password confirmation is required.',
            
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
            'g-recaptcha-response.captcha' => 'reCAPTCHA verification failed. Please try again.',
            
            'timestamp.required' => 'Form timestamp is missing.',
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
            'username' => 'username',
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
        \Log::warning('Admin registration validation failed', [
            'ip' => $this->ip(),
            'email' => $this->input('email'),
            'username' => $this->input('username'),
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
