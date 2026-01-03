# Order Status Workflow & Notifications

## Overview
This document describes the complete order lifecycle, status transitions, and automated notifications sent to customers at each stage.

## Order Statuses

The `orders` table uses an ENUM field with the following valid statuses:

1. **pending** - Order received, awaiting review
2. **processing** - Order is being prepared/processed
3. **payment_received** - Payment confirmed, queued for processing
4. **ready_for_delivery** - Order ready for pickup/delivery
5. **out_for_delivery** - Order dispatched for delivery
6. **delivered** - Order completed and delivered
7. **cancelled** - Order cancelled

## Status Transitions

### Normal Flow
```
pending → processing → payment_received → ready_for_delivery → out_for_delivery → delivered
```

### Alternative Flows
```
pending → cancelled (Customer cancels before processing)
processing → cancelled (Business cancels during processing)
payment_received → cancelled (Refund/cancellation after payment)
```

## Automated Notifications

### 1. Order Placed (Initial Notification)
**Trigger:** When a new order is created  
**Notification:** `App\Notifications\OrderPlaced`  
**Channels:** Email, SMS, Database  
**Status:** `pending`

**Email Content:**
- Order confirmation with order number
- Complete order details (items, quantities, prices)
- Total amount
- Payment information
- Next steps for customer

**SMS Content:**
```
Thank you for your order #ORD-XXX! Total: GHS X,XXX.XX. Status: Pending. We'll notify you once we start processing.
```

**Usage:**
```php
use App\Notifications\OrderPlaced;

// When creating a new order
$order = Order::create([...]);
$customer->notify(new OrderPlaced($order));
```

### 2. Status Changed Notifications
**Trigger:** When order status is updated  
**Notification:** `App\Notifications\OrderStatusChanged`  
**Channels:** Email (for important statuses), SMS (always), Database  

**Important Statuses (Email + SMS):**
- `payment_received`
- `ready_for_delivery`
- `out_for_delivery`
- `delivered`
- `cancelled`

**All Statuses (SMS + Database):**
- All 7 status changes

#### Payment Received
**Email Subject:** Order Status Update - ORD-XXX  
**Message:** "Payment confirmed! We have received your payment. Your order is now in the queue for processing."  
**SMS:** "Order #ORD-XXX: Payment confirmed! Your order will be processed soon."

#### Ready for Delivery
**Email Subject:** Order Status Update - ORD-XXX  
**Message:** "Great news! Your order is ready for pickup or delivery."  
**SMS:** "Your order is ready for pickup/delivery! Contact us to arrange."  
**Action Required:** Customer should contact business to arrange pickup/delivery

#### Out for Delivery
**Email Subject:** Order Status Update - ORD-XXX  
**Message:** "Your order has been dispatched and is out for delivery."  
**SMS:** "Your order is out for delivery and will arrive soon."

#### Delivered
**Email Subject:** Order Status Update - ORD-XXX  
**Message:** "Your order has been delivered. We hope you enjoy your purchase!"  
**SMS:** "Your order has been delivered. Thank you!"

#### Cancelled
**Email Subject:** Order Status Update - ORD-XXX  
**Message:** "Your order has been cancelled as requested."  
**SMS:** "Your order has been cancelled. Contact us if you have questions."  
**Action Required:** Customer should verify cancellation if not requested

## Implementation

### Creating a New Order with Notification
```php
use App\Models\Order;
use App\Models\Customer;
use App\Notifications\OrderPlaced;

// Create the order
$order = Order::create([
    'customer_id' => $customer->id,
    'status' => 'pending',
    'total' => 1250.00,
    // ... other fields
]);

// Add order items
foreach ($items as $item) {
    $order->items()->create($item);
}

// Send initial notification
$customer->notify(new OrderPlaced($order));
```

### Updating Order Status with Automatic Notifications
```php
// Method 1: Using the updateStatus helper (Recommended)
$order->updateStatus('payment_received', 'Payment via Mobile Money');

// Method 2: Update without notification
$order->updateStatus('processing', 'Started processing', notify: false);

// Method 3: Manual status update (Not Recommended - bypasses notifications)
$order->status = 'ready_for_delivery';
$order->save();
// This won't send notifications or record history!
```

### Status Validation
The `updateStatus()` method automatically validates status values:

```php
try {
    $order->updateStatus('completed'); // Invalid status
} catch (\InvalidArgumentException $e) {
    // Handle error: "Invalid order status: completed"
}
```

Valid statuses are enforced to match database enum.

### Checking Order Status
```php
// Check if order has specific status
if ($order->hasStatus('delivered')) {
    // Order is delivered
}

// Check multiple statuses
if ($order->hasStatus(['out_for_delivery', 'delivered'])) {
    // Order is either out for delivery or delivered
}

// Get human-readable status name
echo $order->status_name; // "Ready for delivery" instead of "ready_for_delivery"

// Query orders by status
$pendingOrders = Order::withStatus('pending')->get();
$activeOrders = Order::withStatus(['processing', 'out_for_delivery'])->get();
```

## Notification Channels

### Email (via AWS SES)
- **Configuration:** `.env` - `MAIL_MAILER=ses`
- **From Address:** Configure in `config/mail.php`
- **Templates:** `resources/views/emails/orders/`
- **Queue:** Sent via SQS queue for reliability

### SMS (via FrogSMS)
- **Service:** `App\Services\SMSService`
- **Channel:** `App\Notifications\Channels\SMSChannel`
- **Provider:** FrogSMS API
- **Configuration:** `.env` - `FROGSMS_*` variables
- **Character Limit:** Keep messages under 160 characters

### Database
- **Table:** `notifications`
- **Purpose:** Activity log and notification history
- **Access:** Via Laravel's notification system
- **Retention:** Permanent (configure cleanup if needed)

## Queue Processing

All notifications implement `ShouldQueue` interface for background processing:

```bash
# Start queue worker (development)
php artisan queue:work

# Start queue worker (production - supervisord)
php artisan queue:work sqs --sleep=3 --tries=3
```

## Customer Model Requirements

The Customer model must be Notifiable:

```php
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use Notifiable;
    
    // Phone number is required for SMS notifications
    protected $fillable = ['name', 'email', 'phone', ...];
}
```

## Testing Notifications

### 1. Test Email Locally
```bash
# Use log driver for testing
MAIL_MAILER=log php artisan tinker

>>> $order = Order::first();
>>> $order->customer->notify(new \App\Notifications\OrderPlaced($order));
# Check storage/logs/laravel.log
```

### 2. Test SMS (Use Test Mode)
```php
// In SMSService.php, add test mode
if (config('app.env') === 'local') {
    \Log::info('SMS would be sent', ['to' => $to, 'message' => $message]);
    return true;
}
```

### 3. Test Status Changes
```bash
php artisan tinker

>>> $order = Order::first();
>>> $order->updateStatus('processing');
>>> $order->updateStatus('payment_received', 'Paid via MoMo');
>>> $order->updateStatus('ready_for_delivery');
# Check logs/database for notifications
```

## Troubleshooting

### Notifications Not Sending

**Check 1: Queue Processing**
```bash
# Is queue worker running?
php artisan queue:work

# Check failed jobs
php artisan queue:failed
```

**Check 2: Customer Has Contact Info**
```php
$customer = Customer::find($id);
dd($customer->email, $customer->phone); // Should not be null
```

**Check 3: Notification Channels**
```php
// Check what channels are configured
$notification = new OrderStatusChanged($order, 'pending');
dd($notification->via($customer));
```

**Check 4: AWS Credentials**
```bash
# Verify AWS credentials are configured
php artisan config:clear
php artisan tinker
>>> config('services.ses')
```

### SMS Not Delivering

**Check 1: Phone Format**
```php
// Phone should be in format: +233XXXXXXXXX or 0XXXXXXXXX
$customer->phone = '+233244123456'; // Correct
$customer->phone = '0244123456';    // Also works
```

**Check 2: FrogSMS Credits**
- Log into FrogSMS dashboard
- Check account balance
- Verify API key is active

**Check 3: SMS Service Logs**
```bash
# Check Laravel logs for SMS sending attempts
tail -f storage/logs/laravel.log | grep SMS
```

### Email Not Formatting Correctly

**Check 1: Blade Template**
```bash
# Clear view cache
php artisan view:clear

# Check template exists
ls resources/views/emails/orders/confirmation.blade.php
```

**Check 2: Data Availability**
```php
// In the notification class, add logging
public function toMail($notifiable)
{
    \Log::info('Email data', ['order' => $this->order->toArray()]);
    // ... rest of method
}
```

## Best Practices

1. **Always use `updateStatus()` method** - Don't update status directly
2. **Provide notes for status changes** - Helps with debugging and customer service
3. **Handle failed notifications** - Monitor `failed_jobs` table
4. **Test in staging environment** - Before deploying to production
5. **Keep SMS messages concise** - Under 160 characters to avoid splits
6. **Monitor notification queue** - Ensure timely delivery
7. **Validate status transitions** - Only allow valid status progressions
8. **Log important events** - Status changes, payment confirmations, etc.

## Production Deployment

### Required Environment Variables
```env
# Email (AWS SES)
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
MAIL_FROM_ADDRESS=noreply@realman.com
MAIL_FROM_NAME="RealMan Livestock"

# SMS (FrogSMS)
FROGSMS_API_KEY=your_frogsms_api_key
FROGSMS_SENDER_ID=RealMan
FROGSMS_BASE_URL=https://api.frogsms.com/v1

# Queue (AWS SQS)
QUEUE_CONNECTION=sqs
SQS_QUEUE=realman-production-queue
```

### Supervisor Configuration
```ini
[program:realman-queue-worker]
command=php /path/to/artisan queue:work sqs --sleep=3 --tries=3 --max-time=3600
directory=/path/to/realman
user=www-data
autostart=true
autorestart=true
stopwaitsecs=3600
stdout_logfile=/var/log/realman/queue-worker.log
```

## Order Status Workflow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     Customer Places Order                    │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
                   ┌──────────┐
                   │ PENDING  │ ← Email + SMS: Order Confirmation
                   └────┬─────┘
                        │
                   ┌────┴─────┐
                   │          │
                   ▼          ▼
             ┌──────────┐  ┌───────────┐
             │CANCELLED │  │PROCESSING │ ← SMS: Processing Started
             └──────────┘  └─────┬─────┘
                                 │
                                 ▼
                        ┌─────────────────┐
                        │PAYMENT_RECEIVED │ ← Email + SMS: Payment Confirmed
                        └────────┬────────┘
                                 │
                                 ▼
                        ┌──────────────────┐
                        │READY_FOR_DELIVERY│ ← Email + SMS: Ready for Pickup
                        └────────┬─────────┘
                                 │
                                 ▼
                        ┌──────────────────┐
                        │OUT_FOR_DELIVERY  │ ← Email + SMS: Out for Delivery
                        └────────┬─────────┘
                                 │
                                 ▼
                        ┌──────────────────┐
                        │   DELIVERED      │ ← Email + SMS: Delivered
                        └──────────────────┘
```

## Related Files

- **Notifications:**
  - `app/Notifications/OrderPlaced.php`
  - `app/Notifications/OrderStatusChanged.php`
  - `app/Notifications/Channels/SMSChannel.php`

- **Services:**
  - `app/Services/SMSService.php`

- **Models:**
  - `app/Models/Order.php`
  - `app/Models/Customer.php`
  - `app/Models/OrderStatusHistory.php`

- **Email Templates:**
  - `resources/views/emails/orders/confirmation.blade.php`

- **Configuration:**
  - `config/mail.php`
  - `config/queue.php`
  - `config/services.php`

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review failed jobs: `php artisan queue:failed`
3. Verify environment configuration: `.env`
4. Test notification channels individually
