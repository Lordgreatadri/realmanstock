<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Animal;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data)
    {
        try {
            DB::beginTransaction();

            // Create or get customer
            $customer = null;
            if (isset($data['customer_id'])) {
                $customer = Customer::find($data['customer_id']);
            } elseif (isset($data['customer_phone'])) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $data['customer_phone']],
                    [
                        'name' => $data['customer_name'] ?? 'Guest Customer',
                        'email' => $data['customer_email'] ?? null,
                        'address' => $data['customer_address'] ?? null,
                    ]
                );
            }

            // Calculate totals
            $subtotal = 0;
            $processingFee = 0;

            foreach ($data['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
                if (isset($item['requires_processing']) && $item['requires_processing']) {
                    $processingFee += $item['processing_fee'] ?? 0;
                }
            }

            $deliveryFee = $data['delivery_fee'] ?? 0;
            $discount = $data['discount'] ?? 0;
            $tax = $data['tax'] ?? 0;
            $total = $subtotal + $processingFee + $deliveryFee + $tax - $discount;

            // Create order
            $order = Order::create([
                'customer_id' => $customer?->id,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'processing_fee' => $processingFee,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'balance' => $total,
                'delivery_type' => $data['delivery_type'] ?? 'pickup',
                'delivery_address' => $data['delivery_address'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? null,
                'special_instructions' => $data['special_instructions'] ?? null,
            ]);

            // Create order items and mark animals as reserved/sold
            foreach ($data['items'] as $itemData) {
                $order->items()->create([
                    'item_type' => $itemData['item_type'],
                    'item_id' => $itemData['item_id'],
                    'item_name' => $itemData['item_name'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'] ?? 'piece',
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $itemData['quantity'] * $itemData['unit_price'],
                    'requires_processing' => $itemData['requires_processing'] ?? false,
                    'processing_fee' => $itemData['processing_fee'] ?? 0,
                ]);

                // If item is an animal, update its status
                if ($itemData['item_type'] === 'animal') {
                    $animal = Animal::find($itemData['item_id']);
                    if ($animal) {
                        $animal->update(['status' => 'reserved']);
                    }
                }
            }

            // Create initial status history
            $order->statusHistories()->create([
                'from_status' => null,
                'to_status' => 'pending',
                'notes' => 'Order created',
                'user_id' => auth()->id(),
            ]);

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateOrderStatus(Order $order, string $newStatus, ?string $notes = null)
    {
        try {
            DB::beginTransaction();

            $oldStatus = $order->status;
            $order->update(['status' => $newStatus]);

            $order->statusHistories()->create([
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'notes' => $notes,
                'user_id' => auth()->id(),
            ]);

            // If order is delivered, mark animals as sold
            if ($newStatus === 'delivered') {
                foreach ($order->items as $item) {
                    if ($item->item_type === 'animal') {
                        $animal = Animal::find($item->item_id);
                        if ($animal) {
                            $animal->update(['status' => 'sold']);
                        }
                    }
                }
            }

            // If order is cancelled, free up reserved animals
            if ($newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->item_type === 'animal') {
                        $animal = Animal::find($item->item_id);
                        if ($animal && $animal->status === 'reserved') {
                            $animal->update(['status' => 'available']);
                        }
                    }
                }
            }

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function recordPayment(Order $order, float $amount, string $method)
    {
        try {
            DB::beginTransaction();

            $order->amount_paid += $amount;
            $order->balance = $order->total - $order->amount_paid;
            $order->payment_method = $method;

            // Update order status if fully paid
            if ($order->balance <= 0) {
                $this->updateOrderStatus($order, 'payment_received', 'Payment completed');
            }

            $order->save();

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getOrdersByStatus(string $status, $perPage = 15)
    {
        return Order::with(['customer', 'items', 'user'])
            ->where('status', $status)
            ->latest()
            ->paginate($perPage);
    }

    public function getPendingOrders()
    {
        return $this->getOrdersByStatus('pending');
    }

    public function getTodayOrders()
    {
        return Order::with(['customer', 'items'])
            ->whereDate('created_at', today())
            ->latest()
            ->get();
    }
}
