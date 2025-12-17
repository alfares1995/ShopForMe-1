<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\OrderShipping;

class OrderShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create();

        foreach (range(1, 20) as $i) {
            OrderShipping::create([
                'order_id' => $i,
                'shipping_method_id' => rand(1, 2),
                'carrier' => $faker->company,
                'tracking_number' => strtoupper($faker->bothify('TRK###???')),
                'tracking_url' => $faker->url,
                'shipped_at' => now()->subDays(rand(1, 5)),
                'estimated_delivery' => now()->addDays(rand(1, 5)),
            ]);
        }
    }
}
