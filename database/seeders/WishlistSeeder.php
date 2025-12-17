<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Wishlist;

class WishlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
             foreach (range(1, 50) as $i) {
            Wishlist::create([
                'user_id' => rand(1, 10),
                'product_id' => rand(1, 10),
            ]);
        }
    }
}
