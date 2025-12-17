<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $brands = ['Apple', 'Samsung', 'Nike', 'Adidas', 'Sony'];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand,
                'slug' => Str::slug($brand),
                'description' => "$brand is a leading brand in its industry.",
                'logo' => "logos/{$brand}.png",
                'website' => "https://www." . strtolower($brand) . ".com",
                'is_active' => true,
            ]);
        }
    }
}
