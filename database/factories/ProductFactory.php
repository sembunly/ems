<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brand = $this->faker->randomElement(['Dell', 'HP', 'Lenovo', 'Apple', 'Asus', 'Acer']);
        $series = [
            'Dell' => ['Inspiron', 'XPS', 'Latitude', 'Vostro', 'Alienware', 'Precision'],
            'HP' => ['Pavilion', 'Spectre', 'Envy', 'EliteBook', 'ProBook', 'Omen'],
            'Lenovo' => ['ThinkPad', 'IdeaPad', 'Yoga', 'Legion', 'ThinkBook', 'LOQ'],
            'Apple' => ['MacBook Air', 'MacBook Pro 13', 'MacBook Pro 14', 'MacBook Pro 16'],
            'Asus' => ['ZenBook', 'VivoBook', 'ROG Strix', 'TUF Gaming', 'ProArt', 'Chromebook'],
            'Acer' => ['Aspire', 'Swift', 'Nitro', 'Predator', 'Spin', 'TravelMate'],
        ];
        $seriesName = $this->faker->randomElement($series[$brand]);
        $name = "$brand $seriesName";

        $laptopImages = glob(public_path('images/Laptops/{2in1,gaming,mac,windows}/*.*'), GLOB_BRACE);
        $laptopImages = array_map(fn ($path) => str_replace(public_path() . DIRECTORY_SEPARATOR, '', $path), $laptopImages);
        $laptopImages = array_map(fn ($path) => str_replace('\\', '/', $path), $laptopImages);

        $ram = $this->faker->randomElement(['8GB', '16GB', '32GB']);
        $storage = $this->faker->randomElement(['256GB SSD', '512GB SSD', '1TB SSD']);
        $processor = $this->faker->randomElement(['Intel Core i5', 'Intel Core i7', 'AMD Ryzen 5', 'AMD Ryzen 7', 'Apple M1', 'Apple M2']);
        $screenSize = $this->faker->randomElement(['13.3"', '14"', '15.6"', '17"']);

        $descriptions = [
            "The $name delivers powerful performance with its $processor processor, $ram RAM, and $storage. Perfect for everyday computing and multitasking.",
            "Experience seamless productivity with the $name featuring a stunning $screenSize display, $processor chip, and $storage storage.",
            "Built for professionals on the go, the $name combines a sleek design with $ram memory, $processor power, and a vibrant $screenSize screen.",
            "Whether you're working or streaming, the $name with $processor, $ram RAM, and $storage offers speed and reliability in a portable package.",
            "The $name is a versatile laptop with a $screenSize display, $ram RAM, and $storage — ideal for students, creators, and business users alike.",
            "Unleash your potential with the $name, powered by $processor and equipped with $ram RAM and $storage for lightning-fast performance.",
        ];

        return [
            'category_id' => \App\Models\Category::inRandomOrder()->value('id'),
            'name' => $name,
            'brand' => $brand,
            'model' => $this->faker->bothify('??-####'),
            'price' => $this->faker->randomFloat(2, 300, 3000),
            'stock' => $this->faker->numberBetween(10, 100),
            'image' => $this->faker->randomElement($laptopImages),
            'description' => $this->faker->randomElement($descriptions),
            'ram' => $ram,
            'storage' => $storage,
            'processor' => $processor,
            'screen_size' => $screenSize,
        ];
    }
}
