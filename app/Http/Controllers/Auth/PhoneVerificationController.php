<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SMSService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhoneVerificationController extends Controller
{
    /**
     * Display the phone verification view.
     */
    public function show(): View
    {
        return view('auth.verify-phone');
    }

    /**
     * Handle an incoming phone verification request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if ($user->verifyOTP($request->otp)) {
            $user->update([
                'phone_verified' => true,
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            // Check if user is approved
            if (!$user->is_approved) {
                return redirect()->route('pending-approval');
            }

            return redirect()->intended('/dashboard')->with('status', 'Phone verified successfully!');
        }

        return back()->withErrors(['otp' => 'Invalid or expired verification code.']);
    }

    /**
     * Resend the phone verification code.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->generateOTP();

        // Send OTP via SMS
        $smsService = new SMSService();
        $smsSent = $smsService->sendOTP($user->phone, $user->otp);

        if (!$smsSent) {
            return back()->withErrors(['otp' => 'Failed to send verification code. Please try again.']);
        }
        
        return back()->with('status', 'verification-link-sent');
    }
}
