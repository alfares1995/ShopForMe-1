<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShippingMethod; // Assuming you have a ShippingsMethod model

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $methods = [
            ['name' => 'Standard Shipping', 'price' => 5.00, 'estimated_days_min' => 3, 'estimated_days_max' => 7],
            ['name' => 'Express Shipping', 'price' => 15.00, 'estimated_days_min' => 1, 'estimated_days_max' => 2],
        ];

        foreach ($methods as $method) {
            ShippingMethod::create([
                'name' => $method['name'],
                'price' => $method['price'],
                'free_shipping_threshold' => 100.00,
                'estimated_days_min' => $method['estimated_days_min'],
                'estimated_days_max' => $method['estimated_days_max'],
                'is_active' => true,
                'sort_order' => 1,
            ]);
        }
    }
}
