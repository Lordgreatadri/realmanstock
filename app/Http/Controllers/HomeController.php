<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Animal;
use App\Models\StoreItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $livestockCategories = Category::livestock()
            ->active()
            ->ordered()
            ->withCount(['animals' => function ($query) {
                $query->where('status', 'available');
            }])
            ->get();

        $groceryCategories = Category::grocery()
            ->active()
            ->ordered()
            ->get();

        $featuredAnimals = Animal::available()
            ->with('category')
            ->latest()
            ->limit(6)
            ->get();

        $featuredProducts = StoreItem::active()
            ->with('category')
            ->latest()
            ->limit(6)
            ->get();

        return view('welcome', compact(
            'livestockCategories',
            'groceryCategories',
            'featuredAnimals',
            'featuredProducts'
        ));
    }

    public function order()
    {
        $categories = Category::active()
            ->ordered()
            ->get();

        $animals = Animal::available()
            ->with('category')
            ->get();

        $storeItems = StoreItem::active()
            ->with('category')
            ->get();

        return view('order', compact('categories', 'animals', 'storeItems'));
    }

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:animal,product',
            'items.*.id' => 'required|integer',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Check if customer exists by email or phone
            $customer = Customer::where('email', $validated['customer_email'])
                ->orWhere('phone', $validated['customer_phone'])
                ->first();

            // Auto-create customer if doesn't exist
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                    'phone' => $validated['customer_phone'],
                    'address' => $validated['customer_address'] ?? null,
                    'customer_type' => 'retail',
                    'status' => 'active',
                ]);
            }

            // Calculate total
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            // Create order
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'total' => $subtotal, // Same as subtotal since no fees/discounts from public form
                'notes' => $validated['delivery_notes'] ?? null,
            ]);

            // Create order items
            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => $item['type'],
                    'item_id' => $item['id'],
                    'item_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            DB::commit();

            return redirect()->route('home')
                ->with('success', 'Your order has been placed successfully! Order #' . $order->order_number . '. We will contact you shortly.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the actual error for debugging
            \Log::error('Order placement failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}
