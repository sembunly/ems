<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $computerTypes = ['Gaming Laptop', 'Business Laptop', 'Ultrabook', 'Chromebook', '2-in-1 Convertible', 'Workstation', 'Student Laptop', 'Creator Laptop'];
        $name = $this->faker->unique()->randomElement($computerTypes);
        
        $laptopImages = glob(public_path('images/Laptops/{2in1,gaming,mac,windows}/*.*'), GLOB_BRACE);
        $laptopImages = array_map(fn ($path) => str_replace(public_path() . DIRECTORY_SEPARATOR, '', $path), $laptopImages);
        $laptopImages = array_map(fn ($path) => str_replace('\\', '/', $path), $laptopImages);
        
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => "Shop our latest selection of $name models.",
            'image' => $this->faker->randomElement($laptopImages),
        ];
    }
}
