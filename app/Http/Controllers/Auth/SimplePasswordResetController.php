<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Staff;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SimplePasswordResetController extends Controller
{
    /**
     * Show the password reset form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.simple-forgot-password', [
            'title' => 'Reset Password'
        ]);
    }

    /**
     * Send password reset email
     */
    public function sendResetEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        $email = strtolower(trim($request->input('email')));

        Log::info('Password reset email attempt started', [
            'email' => $email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            // First validate if email exists in database
            $user = Admin::where('email', $email)->first();
            $userType = 'admin';
            
            if (!$user) {
                $user = Staff::where('email', $email)->first();
                $userType = 'staff';
            }
            
            if (!$user) {
                Log::warning('Password reset attempted for non-existent email', [
                    'email' => $email,
                    'ip' => $request->ip()
                ]);
                
                return back()
                    ->withErrors(['email' => 'We could not find an account with that email address.'])
                    ->withInput($request->only('email'));
            }
            
            Log::info('Email validation successful, proceeding with password reset', [
                'email' => $email,
                'user_type' => $userType,
                'user_name' => $user->first_name . ' ' . $user->last_name
            ]);
            
            // Generate unique token
            $token = Str::random(64);
            
            // Store token in password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => hash('sha256', $token),
                    'created_at' => now(),
                    'expires_at' => now()->addHours(1),
                    'used_at' => null,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]
            );
            
            // Send email with reset link
            $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($email));
            
            try {
                Mail::to($email)->send(new PasswordResetMail(
                    $user->first_name . ' ' . $user->last_name,
                    $resetUrl,
                    $request->ip()
                ));
                
                Log::info('Password reset email sent successfully', [
                    'email' => $email,
                    'ip' => $request->ip()
                ]);
                
                return back()->with('status', 
                    'Password reset email sent successfully to ' . $email . '! Please check your email (including spam folder) for the reset link.'
                );
            } catch (\Exception $e) {
                Log::error('Failed to send password reset email', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                    'ip' => $request->ip()
                ]);
                
                return back()->withErrors([
                    'email' => 'Failed to send password reset email. Please try again later.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Password reset email exception', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withErrors(['email' => 'An error occurred while processing your request. Please try again later.'])
                ->withInput($request->only('email'));
        }
    }
}
