<?php

namespace App\Notifications\Channels;

use App\Services\SMSService;
use Illuminate\Notifications\Notification;

class SMSChannel
{
    /**
     * Create a new SMS channel instance.
     */
    public function __construct(
        protected SMSService $smsService
    ) {
    }

    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        // Get phone number from notifiable
        $phone = $notifiable->routeNotificationFor('sms') ?? $notifiable->phone;

        if (!$phone) {
            return;
        }

        // Get SMS message from notification
        if (method_exists($notification, 'toSMS')) {
            $message = $notification->toSMS($notifiable);
        } else {
            return;
        }

        // Send SMS
        $this->smsService->send($phone, $message);
    }
}
