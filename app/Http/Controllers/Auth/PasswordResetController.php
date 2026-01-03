<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SMSService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Display the password reset request form.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset phone request.
     */
    public function sendOTP(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'No account found with this phone number.']);
        }

        // Generate OTP
        $user->generateOTP();

        // Send OTP via SMS
        $smsService = new SMSService();
        $smsSent = $smsService->sendPasswordResetOTP($user->phone, $user->otp);

        if (!$smsSent) {
            return back()->withErrors(['phone' => 'Failed to send verification code. Please try again.']);
        }

        return redirect()->route('password.reset', ['phone' => $request->phone])
            ->with('status', 'Verification code sent to your phone!');
    }

    /**
     * Display the password reset form.
     */
    public function showResetForm(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming password reset request with OTP.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'No account found with this phone number.']);
        }

        if (!$user->verifyOTP($request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired verification code.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        return redirect()->route('login')->with('status', 'Password reset successfully! You can now login.');
    }
}
