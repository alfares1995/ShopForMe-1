<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create();

        foreach (range(1, 30) as $i) {
            Payment::create([
                'order_id' => $i,
                'payment_method' => $faker->randomElement(['stripe', 'paypal']),
                'payment_id' => strtoupper($faker->bothify('PAY###???')),
                'transaction_id' => strtoupper($faker->bothify('TXN###???')),
                'status' => $faker->randomElement(['completed', 'pending', 'failed']),
                'amount' => 125.00,
                'currency' => 'GBP',
                'payment_details' => json_encode(['ref' => $faker->uuid]),
                'paid_at' => now(),
            ]);}
    }
}
