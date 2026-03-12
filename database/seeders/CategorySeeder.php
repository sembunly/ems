<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        if (Category::count() === 0) {
            Category::factory()->count(8)->create();
        }
    }
}