<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $categories = ['Electronics', 'Books', 'Clothing', 'Home & Kitchen', 'Toys', 'Sports'];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "Category for $name",
                'meta_title' => "$name Products",
                'meta_description' => "Find best $name products",
                'sort_order' => rand(1, 10),
                'is_active' => true,
                'parent_id' => null,
            ]);
        }
    }
}
