<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No users or products found. Seed them first.');
            return;
        }

        $statuses = ['pending', 'processing', 'completed', 'cancelled'];

        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();

            // Most people buy 1 laptop, rarely 2
            $numProducts = rand(1, 10) <= 8 ? 1 : 2;
            $orderProducts = $products->random($numProducts);
            $totalAmount = 0;
            $items = [];

            foreach ($orderProducts as $product) {
                $qty = 1;
                $price = $product->price;
                $totalAmount += $price * $qty;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                ];
            }

            // Random date between Jan 2025 and Mar 2026
            $start = strtotime('2025-01-01');
            $end = strtotime('2026-03-13');
            $date = date('Y-m-d H:i:s', rand($start, $end));

            $order = Order::create([
                'user_id' => $user->id,
                'full_name' => $user->name,
                'phone' => '05' . rand(10000000, 99999999),
                'address' => fake()->address(),
                'note' => rand(0, 1) ? fake()->sentence() : null,
                'total_amount' => $totalAmount,
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($items as $item) {
                OrderItem::create(array_merge($item, [
                    'order_id' => $order->id,
                ]));
            }
        }
    }
}
