# Order Notifications Guide

Complete guide for using Email and SMS notifications for orders in the RealMan application.

## ✅ What's Been Created

### 1. **Email Notifications**
- **[app/Mail/OrderConfirmation.php](app/Mail/OrderConfirmation.php)** - Mailable class for order confirmations
- **[resources/views/emails/orders/confirmation.blade.php](resources/views/emails/orders/confirmation.blade.php)** - Email template

### 2. **Multi-Channel Notifications**
- **[app/Notifications/OrderPlaced.php](app/Notifications/OrderPlaced.php)** - Sends both Email & SMS
- **[app/Notifications/Channels/SMSChannel.php](app/Notifications/Channels/SMSChannel.php)** - Custom SMS channel

### 3. **SMS Service Updates**
- **[app/Services/SMSService.php](app/Services/SMSService.php)** - Added order-specific SMS methods

---

## 📧 How to Use Email Notifications

### Method 1: Using Mailable (Email Only)

```php
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;

// In your OrderController
public function store(Request $request)
{
    // Create order
    $order = Order::create($validated);
    
    // Send email immediately
    Mail::to($order->customer->email)->send(new OrderConfirmation($order));
    
    // Or queue email (recommended for better performance)
    Mail::to($order->customer->email)->queue(new OrderConfirmation($order));
    
    return redirect()->back()->with('success', 'Order placed successfully!');
}
```

### Method 2: Using Notification (Email + SMS + Database)

```php
use App\Notifications\OrderPlaced;

// In your OrderController
public function store(Request $request)
{
    // Create order
    $order = Order::create($validated);
    
    // Send notification via email, SMS, and save to database
    $order->customer->notify(new OrderPlaced($order));
    
    // Or queue the notification (recommended)
    $order->customer->notifyNow(new OrderPlaced($order));
    
    return redirect()->back()->with('success', 'Order placed and notifications sent!');
}
```

---

## 📱 SMS Notifications

### Automatic SMS with Notification
The `OrderPlaced` notification automatically sends SMS if customer has a phone number.

### Manual SMS Using SMSService

```php
use App\Services\SMSService;

public function sendOrderNotification(Order $order, SMSService $smsService)
{
    // Order confirmation
    $smsService->sendOrderConfirmation(
        $order->customer->phone,
        $order->order_number,
        $order->total_amount
    );
    
    // Order status update
    $smsService->sendOrderStatusUpdate(
        $order->customer->phone,
        $order->order_number,
        'ready'
    );
}
```

---

## 🔄 Complete Order Workflow Example

### In OrderController.php

```php
<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderPlaced;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Store new order with notifications
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|numeric',
            'items.*.unit_price' => 'required|numeric',
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'delivery_date' => $validated['delivery_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items and calculate total
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);
                $totalAmount += $subtotal;
            }

            // Update total
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            // Send notifications (queued for performance)
            // This sends Email + SMS + Database notification
            $order->customer->notify(new OrderPlaced($order));

            return redirect()->route('manager.orders.index')
                ->with('success', 'Order placed successfully! Customer will receive email and SMS confirmation.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create order: ' . $e->getMessage()]);
        }
    }

    /**
     * Update order status with notification
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,ready,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $validated['status']]);

        // Send status update notification
        if ($oldStatus !== $validated['status']) {
            // Option 1: Send SMS only for status updates
            app(SMSService::class)->sendOrderStatusUpdate(
                $order->customer->phone,
                $order->order_number,
                $validated['status']
            );

            // Option 2: Or create a new notification class for status updates
            // $order->customer->notify(new OrderStatusChanged($order, $validated['status']));
        }

        return redirect()->back()->with('success', 'Order status updated and customer notified!');
    }
}
```

---

## 🎨 Customizing Email Template

Edit **[resources/views/emails/orders/confirmation.blade.php](resources/views/emails/orders/confirmation.blade.php)**

Available variables:
- `$order` - Order model instance
- `$customer` - Customer model instance
- `$items` - Order items collection

Example customizations:

```blade
{{-- Add company logo --}}
<div class="header">
    <img src="{{ asset('images/logo.png') }}" alt="RealMan" style="max-width: 200px;">
    <h1>RealMan Livestock</h1>
</div>

{{-- Add payment information --}}
@if($order->payment_method)
<div class="detail-row">
    <span class="detail-label">Payment Method:</span>
    {{ ucfirst($order->payment_method) }}
</div>
@endif

{{-- Add contact information --}}
<p><strong>Contact Us:</strong></p>
<p>Phone: +233 XX XXX XXXX<br>
Email: info@realman.com</p>
```

---

## 🔔 Creating Additional Notifications

### Order Status Changed Notification

```bash
php artisan make:notification OrderStatusChanged
```

Then configure it:

```php
<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Channels\SMSChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    public function __construct(
        public Order $order,
        public string $oldStatus
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database', SMSChannel::class];
    }

    public function toMail($notifiable): MailMessage
    {
        $statusMessages = [
            'ready' => 'Your order is ready for collection!',
            'completed' => 'Thank you! Your order has been completed.',
            'cancelled' => 'Your order has been cancelled.',
        ];

        return (new MailMessage)
            ->subject('Order Status Update - ' . $this->order->order_number)
            ->line($statusMessages[$this->order->status] ?? 'Your order status has been updated.')
            ->line('New Status: ' . ucfirst($this->order->status))
            ->action('View Order', route('manager.orders.show', $this->order));
    }

    public function toSMS($notifiable): string
    {
        return sprintf(
            "Order #%s status: %s",
            $this->order->order_number,
            ucfirst($this->order->status)
        );
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->status,
        ];
    }
}
```

---

## 🧪 Testing Notifications

### Test Email

```bash
php artisan tinker

$order = App\Models\Order::first();
Mail::to('test@example.com')->send(new App\Mail\OrderConfirmation($order));
```

### Test Notification (Email + SMS)

```bash
php artisan tinker

$customer = App\Models\Customer::first();
$order = $customer->orders()->first();
$customer->notify(new App\Notifications\OrderPlaced($order));
```

### Test SMS Only

```bash
php artisan tinker

$sms = app(App\Services\SMSService::class);
$sms->sendOrderConfirmation('0244123456', 'ORD-ABC123', 500.00);
```

---

## 📊 Database Notifications

View in-app notifications:

```php
// In your controller
public function notifications()
{
    $notifications = auth()->user()->notifications()->latest()->get();
    return view('notifications.index', compact('notifications'));
}

// Mark as read
public function markAsRead($id)
{
    auth()->user()->notifications()->find($id)->markAsRead();
    return redirect()->back();
}
```

---

## 🔄 Queue Configuration

For better performance, make sure queues are running:

```bash
# Development
php artisan queue:work

# Production (via Supervisor)
php artisan queue:work sqs --sleep=3 --tries=3
```

In `.env`:
```env
QUEUE_CONNECTION=sqs  # or database for local development
```

---

## 📝 Summary

**To send order notifications:**

```php
// Simple email only
Mail::to($customer->email)->queue(new OrderConfirmation($order));

// Email + SMS + Database (recommended)
$customer->notify(new OrderPlaced($order));

// SMS only
app(SMSService::class)->sendOrderConfirmation($phone, $orderNumber, $amount);
```

**Files Created:**
- ✅ [app/Mail/OrderConfirmation.php](app/Mail/OrderConfirmation.php)
- ✅ [app/Notifications/OrderPlaced.php](app/Notifications/OrderPlaced.php)
- ✅ [app/Notifications/Channels/SMSChannel.php](app/Notifications/Channels/SMSChannel.php)
- ✅ [resources/views/emails/orders/confirmation.blade.php](resources/views/emails/orders/confirmation.blade.php)
- ✅ SMS methods added to [app/Services/SMSService.php](app/Services/SMSService.php)

All ready to use! 🎉
