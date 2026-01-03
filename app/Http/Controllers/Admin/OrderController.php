<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'user', 'items']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by delivery type
        if ($request->filled('delivery_type')) {
            $query->where('delivery_type', $request->delivery_type);
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        // Filter by balance (paid/unpaid)
        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'paid') {
                $query->where('balance', '<=', 0);
            } elseif ($request->payment_status === 'unpaid') {
                $query->where('balance', '>', 0);
            }
        }

        $orders = $query->latest()->paginate(10);
        $customers = Customer::orderBy('name')->get();

        return view('admin.orders.index', compact('orders', 'customers'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'user', 'items.item', 'statusHistories.user']);
        
        return view('admin.orders.show', compact('order'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $animals = Animal::where('status', 'available')->orderBy('tag_number')->get();
        
        return view('admin.orders.create', compact('customers', 'animals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:delivery_type,delivery|nullable|string',
            'delivery_date' => 'nullable|date',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money,credit',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.animal_id' => 'required|exists:animals,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.processing_type' => 'required|in:live,dressed',
        ]);

        DB::beginTransaction();
        try {
            // Add order items and calculate total first
            $subtotal = 0;
            $itemsData = [];
            
            foreach ($validated['items'] as $item) {
                $animal = Animal::find($item['animal_id']);
                $quantity = $item['quantity'];
                
                // Calculate price based on selling price
                if ($animal->fixed_selling_price) {
                    $unitPrice = $animal->fixed_selling_price;
                } else {
                    $unitPrice = $animal->selling_price_per_kg * $animal->current_weight;
                }
                
                $totalPrice = $unitPrice * $quantity;
                $subtotal += $totalPrice;
                
                $itemsData[] = [
                    'animal' => $animal,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'processing_type' => $item['processing_type'],
                ];
            }
            
            // Calculate final total (subtotal + fees - discount + tax)
            $processingFee = 0;
            $deliveryFee = $validated['delivery_type'] === 'delivery' ? 0 : 0; // Can be calculated based on distance
            $discount = 0;
            $tax = 0;
            $total = $subtotal + $processingFee + $deliveryFee - $discount + $tax;
            
            // Create the order
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'user_id' => auth()->id(),
                'delivery_type' => $validated['delivery_type'],
                'delivery_address' => $validated['delivery_address'],
                'delivery_date' => $validated['delivery_date'],
                'payment_method' => $validated['payment_method'],
                'special_instructions' => $validated['special_instructions'],
                'notes' => $validated['notes'],
                'status' => 'pending',
                'subtotal' => $subtotal,
                'processing_fee' => $processingFee,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'balance' => $total,
            ]);

            // Create order items
            foreach ($itemsData as $itemData) {
                $animal = $itemData['animal'];
                $itemName = "{$animal->tag_number} - {$animal->breed}";
                $requiresProcessing = $itemData['processing_type'] === 'dressed';
                $itemProcessingFee = $requiresProcessing ? 0 : 0; // Can be set based on business logic
                
                $order->items()->create([
                    'item_type' => 'App\Models\Animal',
                    'item_id' => $animal->id,
                    'item_name' => $itemName,
                    'quantity' => $itemData['quantity'],
                    'unit' => 'piece',
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $itemData['total_price'],
                    'requires_processing' => $requiresProcessing,
                    'processing_fee' => $itemProcessingFee,
                    'notes' => 'Processing: ' . ucfirst($itemData['processing_type']),
                ]);
            }

            // Create status history
            $order->statusHistories()->create([
                'from_status' => null,
                'to_status' => 'pending',
                'notes' => 'Order created by admin',
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    public function edit(Order $order)
    {
        $order->load(['customer', 'items']);
        $customers = Customer::orderBy('name')->get();
        
        return view('admin.orders.edit', compact('order', 'customers'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,payment_received,ready_for_delivery,out_for_delivery,delivered,cancelled',
            'payment_method' => 'nullable|in:cash,bank_transfer,mobile_money,credit',
            'delivery_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        // Update status with history if changed
        if ($validated['status'] !== $order->status) {
            $order->updateStatus($validated['status'], $request->status_notes);
        }

        // Record payment if amount provided
        if ($request->filled('amount_paid') && $request->amount_paid > 0) {
            $order->recordPayment($request->amount_paid, $validated['payment_method'] ?? 'cash');
        }

        // Update other fields
        $order->update([
            'delivery_type' => $validated['delivery_type'],
            'delivery_address' => $validated['delivery_address'],
            'delivery_date' => $validated['delivery_date'],
            'special_instructions' => $validated['special_instructions'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        if ($order->status === 'delivered') {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Cannot delete delivered orders.');
        }

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
