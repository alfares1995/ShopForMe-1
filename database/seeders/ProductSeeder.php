<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create();

        foreach (range(1, 50) as $i) {
            Product::create([
                'name' => $faker->words(3, true),
                'slug' => $faker->unique()->slug,
                'description' => $faker->paragraph,
                'short_description' => $faker->sentence,
                'sku' => strtoupper($faker->unique()->bothify('SKU-####')),
                'price' => $faker->randomFloat(2, 10, 500),
                'compare_price' => $faker->optional()->randomFloat(2, 20, 600),
                'cost_price' => $faker->randomFloat(2, 5, 100),
                'stock_quantity' => $faker->numberBetween(0, 100),
                'min_stock_level' => $faker->numberBetween(0, 10),
                'track_stock' => true,
                'stock_status' => $faker->randomElement(['in_stock', 'out_of_stock', 'on_backorder']),
                'weight' => $faker->randomFloat(2, 0.1, 5),
                'dimensions' => $faker->optional()->regexify('\d{2}x\d{2}x\d{2}'),
                'brand_id' => rand(1, 5),
                'meta_title' => $faker->words(5, true),
                'meta_description' => $faker->sentence,
                'is_active' => true,
                'is_featured' => $faker->boolean,
                'is_digital' => false,
                'sort_order' => $faker->numberBetween(0, 100),
            ]);
        }
    }
}

