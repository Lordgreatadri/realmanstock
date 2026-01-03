<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Animal;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create customers
        $customer1 = Customer::firstOrCreate(
            ['email' => 'john.doe@example.com'],
            [
                'name' => 'John Doe',
                'phone' => '+233244123456',
                'address' => '123 Main Street, Accra',
                'city' => 'Accra',
                'state' => 'Greater Accra',
                'allow_credit' => true,
                'credit_limit' => 5000.00,
                'outstanding_balance' => 0.00,
            ]
        );

        $customer2 = Customer::firstOrCreate(
            ['email' => 'jane.smith@example.com'],
            [
                'name' => 'Jane Smith',
                'phone' => '+233244654321',
                'address' => '456 Oak Avenue, Kumasi',
                'city' => 'Kumasi',
                'state' => 'Ashanti',
                'allow_credit' => false,
                'credit_limit' => 0.00,
                'outstanding_balance' => 0.00,
            ]
        );

        // Get admin user
        $admin = User::where('email', 'admin@realman.com')->first();

        // Get some animals
        $animals = Animal::whereNotNull('tag_number')->limit(3)->get();

        if ($animals->count() > 0) {
            $animal1 = $animals[0];
            
            // Order 1 - Pending with balance
            $order1 = Order::create([
                'customer_id' => $customer1->id,
                'user_id' => $admin?->id,
                'status' => 'pending',
                'subtotal' => 850.00,
                'processing_fee' => 50.00,
                'delivery_fee' => 0.00,
                'discount' => 0.00,
                'tax' => 0.00,
                'total' => 900.00,
                'amount_paid' => 500.00,
                'balance' => 400.00,
                'payment_method' => 'mobile_money',
                'delivery_type' => 'pickup',
                'special_instructions' => 'Please call when ready for pickup',
            ]);

            // Add items to order 1
            $order1->items()->create([
                'item_type' => 'App\Models\Animal',
                'item_id' => $animal1->id,
                'item_name' => ($animal1->breed ?? 'Animal') . ' #' . ($animal1->tag_number ?? $animal1->id),
                'quantity' => 1,
                'unit' => 'whole',
                'unit_price' => 850.00,
                'subtotal' => 850.00,
                'requires_processing' => true,
                'processing_fee' => 50.00,
            ]);

            // Order 2 - Processing, fully paid
            if ($animals->count() > 1) {
                $animal2 = $animals[1];
                
                $order2 = Order::create([
                    'customer_id' => $customer2->id,
                    'user_id' => $admin?->id,
                    'status' => 'processing',
                    'subtotal' => 1200.00,
                    'processing_fee' => 100.00,
                    'delivery_fee' => 50.00,
                    'discount' => 50.00,
                    'tax' => 0.00,
                    'total' => 1300.00,
                    'amount_paid' => 1300.00,
                    'balance' => 0.00,
                    'payment_method' => 'bank_transfer',
                    'delivery_type' => 'delivery',
                    'delivery_address' => '456 Oak Avenue, Kumasi',
                    'delivery_date' => now()->addDays(2),
                    'special_instructions' => 'Ring doorbell twice',
                ]);

                $order2->items()->create([
                    'item_type' => 'App\Models\Animal',
                    'item_id' => $animal2->id,
                    'item_name' => ($animal2->breed ?? 'Animal') . ' #' . ($animal2->tag_number ?? $animal2->id),
                    'quantity' => 15,
                    'unit' => 'kg',
                    'unit_price' => 80.00,
                    'subtotal' => 1200.00,
                    'requires_processing' => true,
                    'processing_fee' => 100.00,
                ]);
            }

            // Order 3 - Delivered
            if ($animals->count() > 2) {
                $animal3 = $animals[2];
                
                $order3 = Order::create([
                    'customer_id' => $customer1->id,
                    'user_id' => $admin?->id,
                    'status' => 'delivered',
                    'subtotal' => 450.00,
                    'processing_fee' => 0.00,
                    'delivery_fee' => 30.00,
                    'discount' => 0.00,
                    'tax' => 0.00,
                    'total' => 480.00,
                    'amount_paid' => 480.00,
                    'balance' => 0.00,
                    'payment_method' => 'cash',
                    'delivery_type' => 'delivery',
                    'delivery_address' => '123 Main Street, Accra',
                    'delivery_date' => now()->subDays(1),
                    'created_at' => now()->subDays(3),
                ]);

                $order3->items()->create([
                    'item_type' => 'App\Models\Animal',
                    'item_id' => $animal3->id,
                    'item_name' => ($animal3->breed ?? 'Animal') . ' #' . ($animal3->tag_number ?? $animal3->id),
                    'quantity' => 5,
                    'unit' => 'kg',
                    'unit_price' => 90.00,
                    'subtotal' => 450.00,
                    'requires_processing' => false,
                    'processing_fee' => 0.00,
                ]);

                // Add status history
                $order3->updateStatus('delivered', 'Order delivered successfully');
            }
        }

        $this->command->info('Sample orders created successfully!');
    }
}
