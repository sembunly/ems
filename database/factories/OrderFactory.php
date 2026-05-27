<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentMethods = ['cod', 'card', 'paypal'];
        
        return [
            'user_id' => $user?->id ?? User::factory(),
            'full_name' => $user?->name ?? fake()->name(),
            'phone' => '05' . fake()->numerify('########'),
            'address' => fake()->streetAddress() . ', ' . fake()->city() . ', ' . fake()->country(),
            'note' => fake()->optional(0.3)->sentence(),
            'total_amount' => fake()->randomFloat(2, 300, 5000),
            'status' => fake()->randomElement($statuses),
            'payment_method' => fake()->randomElement($paymentMethods),
            'payment_token' => fake()->optional(0.3)->uuid(),
            'created_at' => fake()->dateTimeBetween('2025-01-01', '2026-04-09'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }
    
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
    
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
    
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
