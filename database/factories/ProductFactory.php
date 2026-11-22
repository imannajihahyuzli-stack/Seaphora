<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
    $types = ['Matte Lipstick', 'Hydrating Serum', 'Liquid Foundation', 'Volumizing Mascara',
              'Eyeshadow Palette', 'Setting Spray', 'Day Cream', 'Cleansing Balm', 'Blush', 'Perfume'];
    $adjectives = ['Velvet', 'Radiant', 'Silk', 'Rose', 'Glow', 'Pure', 'Luxe', 'Dewy'];

    return [
        'brand_id' => Brand::factory(),
        'category_id' => Category::factory(),
        'name' => fake()->randomElement($adjectives) . ' ' . fake()->randomElement($types),
        'description' => fake()->paragraph(),
        'price' => fake()->randomFloat(2, 15, 250),
        'stock' => fake()->numberBetween(0, 100),
        'is_active' => true,
    ];
}
}
