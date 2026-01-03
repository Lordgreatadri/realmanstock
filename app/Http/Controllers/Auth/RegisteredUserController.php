<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\SMSService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:'.User::class],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'company_name' => ['nullable', 'string', 'max:255'],
            'purpose' => ['nullable', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'company_name' => $request->company_name,
            'purpose' => $request->purpose,
            'password' => Hash::make($request->password),
            'is_approved' => false,
            'phone_verified' => false,
        ]);

        // Generate OTP
        $user->generateOTP();

        event(new Registered($user));

        Auth::login($user);

        // Send OTP via SMS
        $smsService = new SMSService();
        $smsSent = $smsService->sendOTP($user->phone, $user->otp);

        if (!$smsSent) {
            session()->flash('warning', 'OTP generated but SMS sending failed. Please check logs.');
        }

        session(['phone' => $user->phone]);

        return redirect()->route('verify-phone');
    }
}
