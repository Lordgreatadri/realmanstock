<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    // SMS / OTP configuration
    'sms' => [
        // driver: 'log' (development), 'twilio' (production)
        'driver' => env('SMS_DRIVER', 'log'),
        'otp_expiry_minutes' => env('OTP_EXPIRY_MINUTES', 10),
        // Twilio settings (if using Twilio)
        'twilio_account_sid' => env('TWILIO_ACCOUNT_SID'),
        'twilio_auth_token' => env('TWILIO_AUTH_TOKEN'),
        'twilio_from' => env('TWILIO_FROM'),
        'twilio_base_url' => env('TWILIO_BASE_URL', 'https://api.twilio.com/2010-04-01'),
    ],
    'frogsms' => [
        'base_url' => env('FROGSMS_BASE_URL', 'https://frog.wigal.com.gh/ismsweb/sendmsg'),
        'password' => env('FROGSMS_PASSWORD'),
        'username' => env('FROGSMS_USERNAME'),
        'senderid' => env('FROGSMS_SENDER_ID'),
    ],
];
