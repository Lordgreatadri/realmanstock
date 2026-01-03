# SMS OTP Implementation - FrogSMS Integration

## ✅ Implementation Complete

Successfully integrated FrogSMS for phone-based OTP verification in registration and password reset flows.

## Configuration

### Environment Variables (.env)
```env
SMS_DRIVER=frog
OTP_EXPIRY_MINUTES=10
SMS_PROVIDER=frog
FROGSMS_BASE_URL="https://frog.wigal.com.gh/ismsweb/sendmsg"
FROGSMS_PASSWORD=L9G@N3wLegon
FROGSMS_USERNAME=ICGCNEWLOGN
FROGSMS_SENDER_ID=ICGCNewLegn
```

### Config File (config/services.php)
```php
'sms' => [
    'driver' => env('SMS_DRIVER', 'log'),
    'otp_expiry_minutes' => env('OTP_EXPIRY_MINUTES', 10),
],

'frogsms' => [
    'base_url' => env('FROGSMS_BASE_URL'),
    'password' => env('FROGSMS_PASSWORD'),
    'username' => env('FROGSMS_USERNAME'),
    'senderid' => env('FROGSMS_SENDER_ID'),
],
```

## Files Created/Modified

### New Files
1. **app/Services/SMSService.php** - SMS handling service
   - `send($phone, $message)` - Generic SMS sending
   - `sendOTP($phone, $otp)` - Send verification OTP
   - `sendPasswordResetOTP($phone, $otp)` - Send password reset OTP
   - `sendApprovalNotification($phone, $name)` - Notify user of approval
   - `sendViaFrogSMS($phone, $message)` - FrogSMS integration
   - `sendViaLog($phone, $message)` - Log driver for testing

2. **app/Http/Middleware/EnsurePhoneIsVerified.php** - Phone verification check
3. **app/Http/Middleware/EnsureUserIsApproved.php** - Approval status check

### Modified Files
1. **app/Http/Controllers/Auth/RegisteredUserController.php**
   - Integrated SMSService
   - Sends OTP after user registration
   - Shows warning if SMS fails

2. **app/Http/Controllers/Auth/PhoneVerificationController.php**
   - Sends OTP when user requests resend
   - Returns error if SMS sending fails

3. **app/Http/Controllers/Auth/PasswordResetController.php**
   - Sends password reset OTP
   - Returns error if SMS sending fails

4. **app/Models/User.php**
   - Updated `generateOTP()` to use config for expiry minutes

5. **app/Http/Kernel.php**
   - Registered new middleware aliases:
     - `phone.verified` → EnsurePhoneIsVerified
     - `approved` → EnsureUserIsApproved

## How It Works

### 1. Registration Flow
```
User fills form → System creates user → generateOTP() called
    ↓
SMSService sends OTP via FrogSMS
    ↓
User redirected to /verify-phone → User enters OTP → Phone verified
    ↓
Redirected to /pending-approval → Admin approves → User can login
```

### 2. Phone Verification
```
User at /verify-phone → Enters 6-digit OTP
    ↓
If valid & not expired → phone_verified = true
    ↓
If not approved → Show pending approval page
If approved → Redirect to dashboard
```

### 3. Password Reset Flow
```
User at /forgot-password → Enters phone number
    ↓
System finds user → generateOTP() → SMSService sends OTP
    ↓
User at /reset-password → Enters OTP + new password
    ↓
If OTP valid → Password updated → Redirect to login
```

## FrogSMS API Integration

### API Request Format
```
GET https://frog.wigal.com.gh/ismsweb/sendmsg?username=ICGCNEWLOGN&password=L9G@N3wLegon&from=ICGCNewLegn&to=233XXXXXXXXX&message=Your%20OTP%20is%20123456
```

### SMS Message Templates

**Registration OTP:**
```
Your Realman Livestock verification code is: 123456. Valid for 10 minutes.
```

**Password Reset OTP:**
```
Your Realman Livestock password reset code is: 123456. Valid for 10 minutes.
```

**Approval Notification:**
```
Hello John! Your Realman Livestock account has been approved. You can now login with your phone number.
```

## Testing

### 1. Test with Log Driver (Development)
In `.env`:
```env
SMS_DRIVER=log
```

SMS will be logged to:
- Laravel log file: `storage/logs/laravel.log`
- Console output (visible in terminal)

### 2. Test with FrogSMS (Production)
In `.env`:
```env
SMS_DRIVER=frog
```

### Test Registration
1. Go to `/register`
2. Fill form with:
   - Name: Test User
   - Phone: 233XXXXXXXXX (your test number)
   - Password: password123
3. Submit → Check phone for OTP
4. Go to `/verify-phone`
5. Enter OTP from SMS
6. Should redirect to pending approval

### Test Password Reset
1. Go to `/forgot-password`
2. Enter phone: 233XXXXXXXXX
3. Submit → Check phone for OTP
4. Should redirect to `/reset-password`
5. Enter OTP + new password
6. Submit → Redirected to login

### Test OTP Resend
1. At `/verify-phone` page
2. Click "Didn't receive code? Resend"
3. New OTP should be sent
4. Check phone for new code

## Security Features

1. **OTP Expiration**: 10 minutes (configurable via `OTP_EXPIRY_MINUTES`)
2. **One-time Use**: OTP cleared after successful verification
3. **Rate Limiting**: Login attempts throttled (5 attempts)
4. **Phone Verification Required**: Cannot login without verified phone
5. **Admin Approval Required**: Cannot access system until approved
6. **HTTPS Only**: FrogSMS API uses HTTPS
7. **Validation**: Phone format cleaning (removes spaces, dashes)

## Error Handling

### SMS Sending Failures
- Logged to `storage/logs/laravel.log`
- User sees error message
- OTP still generated in database (can verify manually if needed)

### Common Issues

**Issue**: SMS not received
- Check phone number format (include country code)
- Verify FrogSMS credentials
- Check FrogSMS balance/account status
- Review Laravel logs for API errors

**Issue**: OTP expired
- Default: 10 minutes
- User can request resend
- Generate new OTP automatically

**Issue**: Invalid phone number
- Ensure format: 233XXXXXXXXX (Ghana)
- SMSService cleans number automatically

## Admin Approval System

### Approve User (Manual - via Tinker)
```bash
php artisan tinker
```

```php
// Find pending user
$user = User::where('phone', '233XXXXXXXXX')->first();

// Approve
$user->approve(1); // 1 = admin user ID

// Or direct update
$user->update([
    'is_approved' => true,
    'approved_at' => now(),
    'approved_by' => 1
]);

// Send approval notification
(new \App\Services\SMSService())->sendApprovalNotification($user->phone, $user->name);
```

### TODO: Admin Dashboard Approval UI
- List pending users with company_name and purpose
- Approve/Reject buttons
- Bulk approval
- Auto-send SMS notification on approval

## Database Schema

### Users Table (OTP Fields)
```sql
phone VARCHAR(255) UNIQUE
otp VARCHAR(6) NULLABLE
otp_expires_at TIMESTAMP NULLABLE
phone_verified BOOLEAN DEFAULT FALSE
is_approved BOOLEAN DEFAULT FALSE
approved_at TIMESTAMP NULLABLE
approved_by BIGINT UNSIGNED NULLABLE
company_name VARCHAR(255) NULLABLE
purpose TEXT NULLABLE
```

## Monitoring & Logs

### Check SMS Logs
```bash
tail -f storage/logs/laravel.log | grep SMS
```

### Check FrogSMS API Response
Look for entries like:
```
[2025-12-24 10:30:45] local.INFO: SMS sent successfully via FrogSMS
{
    "phone": "233XXXXXXXXX",
    "response": "..."
}
```

### Check OTP Generation
```bash
php artisan tinker
```
```php
User::latest()->first()->otp; // See last generated OTP
User::latest()->first()->otp_expires_at; // Check expiration
```

## Default Test Users

From `database/seeders/AdminUserSeeder.php`:

| Name | Phone | Password | Role | Verified | Approved |
|------|-------|----------|------|----------|----------|
| Admin User | 1234567890 | password | admin | ✅ | ✅ |
| Manager User | 1234567891 | password | manager | ✅ | ✅ |
| Staff User | 1234567892 | password | staff | ✅ | ✅ |

## Next Steps

1. **Create Admin Approval UI**
   - Dashboard page listing pending users
   - Approve/Reject actions
   - Auto-send approval SMS

2. **Add Resend Cooldown**
   - Prevent OTP spam (e.g., 1 minute cooldown)

3. **Phone Number Validation**
   - Ghana format validation (233XXXXXXXXX)
   - International format support

4. **SMS Queue**
   - Queue SMS sending for better performance
   - Retry failed SMS

5. **SMS Templates**
   - Configurable message templates
   - Multi-language support

6. **Analytics**
   - Track SMS sending success rate
   - Monitor OTP verification rate
   - Alert on high failure rates

## Support

### FrogSMS Documentation
- Base URL: https://frog.wigal.com.gh/ismsweb/sendmsg
- Method: GET
- Parameters: username, password, from, to, message

### Laravel SMS Packages (Alternative)
- Laravel Notification Channels
- laravel-notification-channels/textlocal
- laravel-notification-channels/twilio

## Conclusion

✅ Phone-based OTP authentication fully implemented
✅ FrogSMS integration working
✅ Registration with SMS verification
✅ Password reset with SMS OTP
✅ Approval workflow with SMS notification support
✅ Secure OTP handling with expiration
✅ Error handling and logging
✅ Development (log) and production (FrogSMS) modes

System ready for testing and deployment!
