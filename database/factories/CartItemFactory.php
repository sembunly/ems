<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        
        return [
            'cart_id' => Cart::factory(),
            'product_id' => $product?->id ?? Product::factory(),
            'quantity' => fake()->numberBetween(1, 5),
            'price' => $product?->price ?? fake()->randomFloat(2, 300, 3000),
        ];
    }
}
