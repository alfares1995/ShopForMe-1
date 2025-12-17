<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create();

        foreach (range(1, 30) as $i) {
            Review::create([
                'product_id' => rand(1, 50),
                'user_id' => rand(1, 20),
                'order_id' => rand(1, 30),
                'reviewer_name' => $faker->name,
                'reviewer_email' => $faker->safeEmail,
                'rating' => rand(1, 5),
                'title' => $faker->sentence(3),
                'comment' => $faker->paragraph,
                'is_verified' => $faker->boolean,
                'is_approved' => true,
            ]);}
    }
}
