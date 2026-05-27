<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have categories first
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Run CategorySeeder first.');
            return;
        }

        // Create 50 products with realistic data
        Product::factory()
            ->count(50)
            ->sequence(function ($sequence) use ($categories) {
                return [
                    'category_id' => $categories->random()->id,
                ];
            })
            ->create();
    }
}
