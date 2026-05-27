<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'customer')->get();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No customers or products found. Seed them first.');
            return;
        }

        // Create carts for about 30% of customers
        $customersWithCarts = $users->random(min((int)($users->count() * 0.3), 15));

        foreach ($customersWithCarts as $user) {
            $cart = Cart::create([
                'user_id' => $user->id,
            ]);

            // Add 1-4 items to each cart
            $numItems = rand(1, 4);
            $cartProducts = $products->random($numItems);

            foreach ($cartProducts as $product) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'price' => $product->price,
                ]);
            }
        }

        $this->command->info("Created {$customersWithCarts->count()} carts with items.");
    }
}
