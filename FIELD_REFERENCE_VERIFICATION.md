# Field Reference Verification

## Database Schema vs Code References

This document verifies that all field references in the notification system match the actual database schema.

---

## Orders Table Schema

```sql
Table: orders
├── id (bigint UN AI PK)
├── uuid (varchar 255)
├── order_number (varchar 255)
├── customer_id (bigint UN)
├── user_id (bigint UN)
├── status (enum: pending, processing, payment_received, ready_for_delivery, out_for_delivery, delivered, cancelled)
├── subtotal (decimal 10,2)
├── processing_fee (decimal 10,2)
├── delivery_fee (decimal 10,2)
├── discount (decimal 10,2)
├── tax (decimal 10,2)
├── total (decimal 10,2)
├── amount_paid (decimal 10,2)
├── balance (decimal 10,2)
├── payment_method (enum: cash, bank_transfer, mobile_money, credit)
├── delivery_type (enum: pickup, delivery)
├── delivery_address (text)
├── delivery_date (datetime)
├── special_instructions (text)
├── notes (text)
├── created_at (timestamp)
├── updated_at (timestamp)
└── deleted_at (timestamp)
```

---

## Field References in Notifications

### ✅ OrderPlaced Notification
**File:** `app/Notifications/OrderPlaced.php`

| Field Referenced | Database Field | Status |
|-----------------|----------------|--------|
| `$order->order_number` | `order_number` | ✅ Valid |
| `$order->total` | `total` | ✅ Valid |
| `$order->status` | `status` | ✅ Valid |
| `$order->id` | `id` | ✅ Valid |

**Email Message:**
```php
->line('Order Number: **' . $this->order->order_number . '**')
->line('Total Amount: GH₵ ' . number_format($this->order->total, 2))
->line('Status: ' . ucfirst(str_replace('_', ' ', $this->order->status)))
```

**SMS Message:**
```php
sprintf(
    "Thank you for your order! Order #%s for GH₵%s has been placed.",
    $this->order->order_number,
    number_format($this->order->total, 2)
)
```

**Database Log:**
```php
[
    'order_id' => $this->order->id,
    'order_number' => $this->order->order_number,
    'total' => $this->order->total,
    'status' => $this->order->status,
]
```

---

### ✅ OrderStatusChanged Notification
**File:** `app/Notifications/OrderStatusChanged.php`

| Field Referenced | Database Field | Status |
|-----------------|----------------|--------|
| `$order->order_number` | `order_number` | ✅ Valid |
| `$order->status` | `status` | ✅ Valid |
| `$order->id` | `id` | ✅ Valid |

**Email Subject:**
```php
'Order Status Update - ' . $this->order->order_number
```

**Email Content:**
```php
->line('Order Number: **' . $this->order->order_number . '**')
->line('New Status: **' . ucfirst(str_replace('_', ' ', $this->order->status)) . '**')
```

**SMS Message:**
```php
sprintf(
    "Order #%s: %s",
    $this->order->order_number,
    $statusInfo['sms']
)
```

**Database Log:**
```php
[
    'order_id' => $this->order->id,
    'order_number' => $this->order->order_number,
    'old_status' => $this->oldStatus,
    'new_status' => $this->order->status,
]
```

---

### ✅ OrderConfirmation Email Template
**File:** `resources/views/emails/orders/confirmation.blade.php`

| Field Referenced | Database Field | Status |
|-----------------|----------------|--------|
| `$order->order_number` | `order_number` | ✅ Valid |
| `$order->created_at` | `created_at` | ✅ Valid |
| `$order->status` | `status` | ✅ Valid |
| `$order->delivery_date` | `delivery_date` | ✅ Valid |
| `$order->notes` | `notes` | ✅ Valid |
| `$order->total` | `total` | ✅ Valid |
| `$customer->name` | (Customer table) | ✅ Valid |

**Order Details Section:**
```blade
<div class="order-number">Order #{{ $order->order_number }}</div>

<div class="detail-row">
    <span class="detail-label">Date:</span>
    {{ $order->created_at->format('F d, Y h:i A') }}
</div>

<div class="detail-row">
    <span class="detail-label">Status:</span>
    <span class="status-badge status-{{ $order->status }}">
        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
    </span>
</div>

@if($order->delivery_date)
<div class="detail-row">
    <span class="detail-label">Expected Delivery:</span>
    {{ \Carbon\Carbon::parse($order->delivery_date)->format('F d, Y') }}
</div>
@endif

@if($order->notes)
<div class="detail-row">
    <span class="detail-label">Notes:</span>
    {{ $order->notes }}
</div>
@endif
```

**Total Display:**
```blade
<td>GH₵ {{ number_format($order->total, 2) }}</td>
```

---

## Order Items Schema

```sql
Table: order_items
├── id (bigint UN AI PK)
├── order_id (bigint UN)
├── item_type (varchar)
├── item_id (bigint UN)
├── item_name (varchar)
├── quantity (decimal 10,2)
├── unit (varchar)
├── unit_price (decimal 10,2)
├── subtotal (decimal 10,2)
├── requires_processing (boolean)
├── processing_fee (decimal 10,2)
└── notes (text)
```

### ✅ Order Items in Email Template

| Field Referenced | Database Field | Status |
|-----------------|----------------|--------|
| `$item->item_name` | `item_name` | ✅ Valid |
| `$item->quantity` | `quantity` | ✅ Valid |
| `$item->unit` | `unit` | ✅ Valid |
| `$item->unit_price` | `unit_price` | ✅ Valid |
| `$item->subtotal` | `subtotal` | ✅ Valid |

**Items Table:**
```blade
@foreach($items as $item)
<tr>
    <td>{{ $item->item_name }}</td>
    <td>{{ $item->quantity }} {{ $item->unit }}</td>
    <td>GH₵ {{ number_format($item->unit_price, 2) }}</td>
    <td>GH₵ {{ number_format($item->subtotal, 2) }}</td>
</tr>
@endforeach
```

---

## Order Model Helper Methods

**File:** `app/Models/Order.php`

### updateStatus() Method
```php
public function updateStatus($newStatus, $notes = null, $notify = true)
{
    // Validates against enum values
    $validStatuses = [
        'pending', 
        'processing', 
        'payment_received', 
        'ready_for_delivery', 
        'out_for_delivery', 
        'delivered', 
        'cancelled'
    ];
    
    // Updates fields: status
    $this->status = $newStatus;
    $this->save();
}
```

### Status Helper Methods
```php
// Returns boolean
hasStatus($statuses)

// Returns string attribute (computed)
getStatusNameAttribute() // Uses: $this->status

// Query scope
scopeWithStatus($query, $statuses) // Filters: status
```

---

## SMS Service Methods

**File:** `app/Services/SMSService.php`

### sendOrderConfirmation()
```php
public function sendOrderConfirmation(Order $order, Customer $customer)
{
    // References:
    $order->order_number  // ✅ Valid
    $order->total         // ✅ Valid
    $order->status        // ✅ Valid
    $customer->phone      // ✅ Valid (Customer table)
}
```

### sendOrderStatusUpdate()
```php
public function sendOrderStatusUpdate(Order $order, Customer $customer, string $oldStatus, string $newStatus)
{
    // References:
    $order->order_number  // ✅ Valid
    $newStatus            // ✅ Validated against enum
    $customer->phone      // ✅ Valid (Customer table)
}
```

**Status Messages Map:**
All 7 status messages match database enum values:
- `pending` ✅
- `processing` ✅
- `payment_received` ✅
- `ready_for_delivery` ✅
- `out_for_delivery` ✅
- `delivered` ✅
- `cancelled` ✅

---

## Validation Summary

### ✅ All Fields Verified
- **Order fields:** All references match database schema
- **Order Item fields:** All references match database schema
- **Status values:** All match enum definition
- **Relationships:** Customer relationship properly used
- **Date formatting:** Uses Laravel Carbon with valid timestamp fields
- **Decimal formatting:** Proper number_format() for all decimal fields

### ✅ No Issues Found
- ❌ No invalid field names
- ❌ No deprecated field references
- ❌ No missing relationships
- ❌ No type mismatches

### ✅ Type Safety
| Field | Type | Usage |
|-------|------|-------|
| `id` | bigint | Direct reference |
| `order_number` | varchar | String concatenation |
| `total` | decimal(10,2) | number_format($value, 2) |
| `status` | enum | String comparison, ucfirst() |
| `created_at` | timestamp | Carbon::parse() / ->format() |
| `delivery_date` | datetime | Carbon::parse() / ->format() |

---

## Testing Checklist

### Runtime Verification

```php
// Test all field references
php artisan tinker

>>> $order = Order::first();
>>> $order->order_number    // Should return string
>>> $order->total           // Should return decimal
>>> $order->status          // Should return enum value
>>> $order->created_at      // Should return Carbon instance
>>> $order->delivery_date   // Should return Carbon instance or null
>>> $order->notes           // Should return text or null

>>> $item = $order->items->first();
>>> $item->item_name        // Should return string
>>> $item->quantity         // Should return decimal
>>> $item->unit_price       // Should return decimal
>>> $item->subtotal         // Should return decimal

>>> $customer = $order->customer;
>>> $customer->name         // Should return string
>>> $customer->phone        // Should return string or null
```

### Notification Test

```php
php artisan tinker

>>> $order = Order::first();
>>> $customer = $order->customer;

// Test OrderPlaced
>>> $notification = new \App\Notifications\OrderPlaced($order);
>>> $notification->toMail($customer);  // Should not error
>>> $notification->toSMS($customer);   // Should not error
>>> $notification->toArray($customer); // Should not error

// Test OrderStatusChanged
>>> $notification = new \App\Notifications\OrderStatusChanged($order, 'pending');
>>> $notification->toMail($customer);  // Should not error
>>> $notification->toSMS($customer);   // Should not error
>>> $notification->toArray($customer); // Should not error
```

---

## Conclusion

✅ **All field references are correct and match the database schema.**

All notification files, email templates, and helper methods use the correct field names from the orders and order_items tables. No runtime errors should occur due to field name mismatches.

### Key Points:
1. Uses `total` (not `total_amount`) ✅
2. Uses `item_name` (not `product_name`) ✅
3. All 7 enum status values properly referenced ✅
4. All relationships properly accessed ✅
5. All decimal fields properly formatted ✅
6. All date fields properly parsed ✅
