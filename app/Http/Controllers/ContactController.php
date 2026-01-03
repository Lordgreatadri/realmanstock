<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\User;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:5000',
        ]);

        // Save the contact message
        $contactMessage = ContactMessage::create($validated);

        // Get admins for notifications
        try {
            $admins = User::role('admin')->get();
            
            if ($admins->isNotEmpty()) {
                foreach ($admins as $admin) {
                    // Send email notification (detailed)
                    try {
                        Mail::raw(
                            "New contact message received:\n\n" .
                            "From: {$validated['name']}\n" .
                            "Email: {$validated['email']}\n" .
                            "Phone: {$validated['phone']}\n\n" .
                            "Message:\n{$validated['message']}\n\n" .
                            "View in admin panel: " . route('admin.contact.index'),
                            function ($message) use ($admin, $validated) {
                                $message->to($admin->email)
                                    ->subject('New Contact Form Submission - ' . $validated['name']);
                            }
                        );
                    } catch (\Exception $e) {
                        // Log email failure but continue
                        Log::warning("Failed to send contact notification email to {$admin->email}: " . $e->getMessage());
                    }

                    // Send SMS notification (immediate alert)
                    if (!empty($admin->phone)) {
                        try {
                            $smsMessage = "New contact message from {$validated['name']} ({$validated['phone']}). " .
                                        "Check admin panel for details.";
                            
                            $this->smsService->send($admin->phone, $smsMessage);
                        } catch (\Exception $e) {
                            // Log SMS failure but continue
                            Log::warning("Failed to send contact notification SMS to {$admin->phone}: " . $e->getMessage());
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            Log::error('Contact form notification error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
}
