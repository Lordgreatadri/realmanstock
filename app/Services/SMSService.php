<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMSService
{
    /**
     * Send SMS using configured provider
     *
     * @param string $phone Phone number with country code
     * @param string $message Message content
     * @return bool Success status
     */
    public function send(string $phone, string $message): bool
    {
        $driver = config('services.sms.driver');

        try {
            switch ($driver) {
                case 'frog':
                    return $this->sendViaFrogSMS($phone, $message);
                case 'log':
                    return $this->sendViaLog($phone, $message);
                default:
                    Log::warning("Unsupported SMS driver: {$driver}");
                    return false;
            }
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage(), [
                'phone' => $phone,
                'driver' => $driver,
                'exception' => $e,
            ]);
            return false;
        }
    }

    /**
     * Send OTP via SMS
     *
     * @param string $phone Phone number
     * @param string $otp OTP code
     * @return bool Success status
     */
    public function sendOTP(string $phone, string $otp): bool
    {
        $message = "Your Realman Livestock verification code is: {$otp}. Valid for " . 
                   config('services.sms.otp_expiry_minutes', 10) . " minutes.";
        
        return $this->send($phone, $message);
    }

    /**
     * Send password reset OTP via SMS
     *
     * @param string $phone Phone number
     * @param string $otp OTP code
     * @return bool Success status
     */
    public function sendPasswordResetOTP(string $phone, string $otp): bool
    {
        $message = "Your Realman Livestock password reset code is: {$otp}. Valid for " . 
                   config('services.sms.otp_expiry_minutes', 10) . " minutes.";
        
        return $this->send($phone, $message);
    }

    /**
     * Send welcome message after approval
     *
     * @param string $phone Phone number
     * @param string $name User name
     * @return bool Success status
     */
    public function sendApprovalNotification(string $phone, string $name): bool
    {
        $message = "Hello {$name}! Your Realman Livestock account has been approved. You can now login with your phone number.";
        
        return $this->send($phone, $message);
    }

    /**
     * Send order confirmation notification
     *
     * @param string $phone Phone number
     * @param string $orderNumber Order number
     * @param float $totalAmount Order total amount
     * @return bool Success status
     */
    public function sendOrderConfirmation(string $phone, string $orderNumber, float $totalAmount): bool
    {
        $message = sprintf(
            "Thank you for your order! Order #%s for GH₵%s has been placed. We'll notify you when ready for collection.",
            $orderNumber,
            number_format($totalAmount, 2)
        );
        
        return $this->send($phone, $message);
    }

    /**
     * Send order status update notification
     *
     * @param string $phone Phone number
     * @param string $orderNumber Order number
     * @param string $status Order status
     * @return bool Success status
     */
    public function sendOrderStatusUpdate(string $phone, string $orderNumber, string $status): bool
    {
        $statusMessages = [
            'pending' => 'Your order has been received and is being reviewed',
            'processing' => 'Your order is being prepared',
            'payment_received' => 'Payment confirmed! Your order is being processed',
            'ready_for_delivery' => 'Your order is ready for pickup/delivery',
            'out_for_delivery' => 'Your order is out for delivery',
            'delivered' => 'Your order has been delivered. Thank you!',
            'cancelled' => 'Your order has been cancelled',
        ];

        $statusText = $statusMessages[$status] ?? ucfirst(str_replace('_', ' ', $status));
        $message = "Order #{$orderNumber} update: {$statusText}.";
        
        return $this->send($phone, $message);
    }

    /**
     * Send SMS via FrogSMS provider
     *
     * @param string $phone Phone number
     * @param string $message Message content
     * @return bool Success status
     */
    protected function sendViaFrogSMS(string $phone, string $message): bool
    {
        $baseUrl = config('services.frogsms.base_url');
        $username = config('services.frogsms.username');
        $password = config('services.frogsms.password');
        $senderId = config('services.frogsms.senderid');

        // Clean phone number (remove spaces, dashes)
        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);

        // Build FrogSMS URL with query parameters
        $url = $baseUrl . 
               '?username=' . urlencode($username) .
               '&password=' . urlencode($password) .
               '&from=' . urlencode($senderId) .
               '&to=' . urlencode($cleanPhone) .
               '&message=' . urlencode($message);

        try {
            $response = Http::timeout(70)->get($url);

            if ($response->successful()) {
                Log::info('SMS sent successfully via FrogSMS', [
                    'phone' => $cleanPhone,
                    'response' => $response->body(),
                ]);
                return true;
            }

            Log::error('FrogSMS API request failed', [
                'phone' => $cleanPhone,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('FrogSMS request exception', [
                'phone' => $cleanPhone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Log SMS (for development/testing)
     *
     * @param string $phone Phone number
     * @param string $message Message content
     * @return bool Always true
     */
    protected function sendViaLog(string $phone, string $message): bool
    {
        Log::info('SMS (Log Driver)', [
            'to' => $phone,
            'message' => $message,
        ]);

        // In development, we'll also output to console for visibility
        echo "\n========================================\n";
        echo "SMS TO: {$phone}\n";
        echo "MESSAGE: {$message}\n";
        echo "========================================\n";

        return true;
    }
}
