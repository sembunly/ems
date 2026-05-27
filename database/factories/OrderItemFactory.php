<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        $quantity = fake()->numberBetween(1, 3);
        $price = $product?->price ?? fake()->randomFloat(2, 300, 3000);
        
        return [
            'order_id' => Order::factory(),
            'product_id' => $product?->id ?? Product::factory(),
            'quantity' => $quantity,
            'price' => $price,
        ];
    }
}
