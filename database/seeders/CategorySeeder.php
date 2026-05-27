<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Gaming Laptop',
                'description' => 'High-performance laptops designed for gaming enthusiasts with powerful GPUs and fast refresh rates.',
                'image' => 'images/Laptops/gaming/Asus-ROG-16.jpg',
            ],
            [
                'name' => 'Business Laptop',
                'description' => 'Professional laptops built for productivity, security, and enterprise use.',
                'image' => 'images/Laptops/windows/Dell-XPS-13.jpg',
            ],
            [
                'name' => 'Ultrabook',
                'description' => 'Sleek, lightweight, and portable laptops perfect for on-the-go professionals.',
                'image' => 'images/Laptops/mac/Macbook-Air-M2.jpg',
            ],
            [
                'name' => 'Chromebook',
                'description' => 'Affordable, cloud-based laptops ideal for students and basic computing tasks.',
                'image' => 'images/Laptops/2in1/Acer-Spin-5.jpg',
            ],
            [
                'name' => '2-in-1 Convertible',
                'description' => 'Versatile laptops that convert to tablets for flexible work and creativity.',
                'image' => 'images/Laptops/2in1/Dell-XPS-13-2in1.jpg',
            ],
            [
                'name' => 'Workstation',
                'description' => 'Powerhouse laptops for creative professionals, engineers, and data scientists.',
                'image' => 'images/Laptops/windows/Dell-Precision-7670.jpg',
            ],
            [
                'name' => 'Student Laptop',
                'description' => 'Budget-friendly laptops perfect for students with essential features.',
                'image' => 'images/Laptops/windows/HP-Pavilion-15.jpg',
            ],
            [
                'name' => 'Creator Laptop',
                'description' => 'High-end laptops optimized for content creation, video editing, and design work.',
                'image' => 'images/Laptops/mac/Macbook-Pro-16.jpg',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'image' => $category['image'],
            ]);
        }
    }
}
