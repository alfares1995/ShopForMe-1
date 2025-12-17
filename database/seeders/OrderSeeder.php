<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create();

        foreach (range(1, 30) as $i) {
            Order::create([
                'order_number' => strtoupper($faker->unique()->bothify('ORD###??')),
                'user_id' => rand(1, 20),
                'guest_email' => null,
                'status' => $faker->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
                'subtotal' => 100.00,
                'tax_amount' => 20.00,
                'shipping_amount' => 10.00,
                'discount_amount' => 5.00,
                'total_amount' => 125.00,
                'currency' => 'GBP',
                'coupon_id' => null,
                'notes' => $faker->optional()->sentence,
                'shipped_at' => $faker->optional()->dateTimeThisYear(),
                'delivered_at' => $faker->optional()->dateTimeThisYear(),
            ]);
        }
    }
}
