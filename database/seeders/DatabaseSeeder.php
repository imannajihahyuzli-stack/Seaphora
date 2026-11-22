<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users (password for all: "password")
        User::factory()->create(['name' => 'Admin', 'email' => 'admin@seaphora.test', 'role' => 'admin']);
        User::factory()->create(['name' => 'Staff', 'email' => 'staff@seaphora.test', 'role' => 'staff']);

        foreach ([1, 2, 3] as $i) {
            User::factory()->create([
                'name' => "Customer $i",
                'email' => "customer$i@seaphora.test",
                'role' => 'customer',
            ]);
        }

        // Brands
        $brands = collect(['Glowlab', 'Rouge Atelier', 'Dewdrop', 'Velour', 'Aurelle'])
            ->map(fn ($name) => Brand::create([
                'name' => $name,
                'description' => "$name beauty and skincare.",
            ]));

        // Categories
        $categories = collect(['Skincare', 'Makeup', 'Fragrance', 'Hair', 'Bath & Body', 'Tools'])
            ->map(fn ($name) => Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]));

        // 25 products spread across brands and categories
        Product::factory()
            ->count(25)
            ->state(fn () => [
                'brand_id' => $brands->random()->id,
                'category_id' => $categories->random()->id,
            ])
            ->create();
    }
}
