<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Animal;
use App\Models\StoreItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'items.item']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(15);
        $customers = Customer::orderBy('name')->get();

        return view('manager.orders.index', compact('orders', 'customers'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        $animals = Animal::where('status', 'available')->with('category')->get();
        $storeItems = StoreItem::where('quantity', '>', 0)->with('category')->get();

        return view('manager.orders.create', compact('customers', 'animals', 'storeItems'));
    }

    public function store(Request $request): RedirectResponse
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

        \DB::beginTransaction();
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
            
            // Calculate final total
            $processingFee = 0;
            $deliveryFee = $validated['delivery_type'] === 'delivery' ? 0 : 0;
            $discount = 0;
            $tax = 0;
            $total = $subtotal + $processingFee + $deliveryFee - $discount + $tax;

            // Create the order
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'delivery_type' => $validated['delivery_type'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'delivery_date' => $validated['delivery_date'] ?? null,
                'payment_method' => $validated['payment_method'],
                'special_instructions' => $validated['special_instructions'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $subtotal,
                'processing_fee' => $processingFee,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'balance' => $total,
                'status' => 'pending',
            ]);

            // Create order items
            foreach ($itemsData as $itemData) {
                $order->items()->create([
                    'item_type' => Animal::class,
                    'item_id' => $itemData['animal']->id,
                    'item_name' => $itemData['animal']->tag_number . ' - ' . $itemData['animal']->breed,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $itemData['total_price'],
                    'total_price' => $itemData['total_price'],
                    'processing_type' => $itemData['processing_type'],
                ]);
            }

            \DB::commit();

            return redirect()->route('manager.orders.index')
                ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Failed to create order: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'items.item']);
        return view('manager.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $order->load(['customer', 'items.item']);
        $customers = Customer::orderBy('name')->get();
        $animals = Animal::where('status', 'available')->with('category')->get();
        $storeItems = StoreItem::where('quantity', '>', 0)->with('category')->get();

        return view('manager.orders.edit', compact('order', 'customers', 'animals', 'storeItems'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_date' => 'nullable|date',
            'delivery_address' => 'nullable|string',
            'payment_status' => 'required|in:pending,partial,paid',
            'status' => 'required|in:pending,confirmed,processing,ready,delivered,cancelled',
            'notes' => 'nullable|string'
        ]);

        $order->update($validated);

        return redirect()->route('manager.orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('manager.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
