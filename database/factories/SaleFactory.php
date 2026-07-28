<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    public function definition()
    {
        $birds = $this->faker->numberBetween(25, 150);
        $averageWeight = $this->faker->randomFloat(3, 1.6, 2.6);
        $totalWeight = $birds * $averageWeight;
        $rate = $this->faker->randomFloat(2, 160, 230);
        $totalAmount = $totalWeight * $rate;

        return [
            'batch_id' => Batch::factory(),
            'sale_date' => now()->toDateString(),
            'buyer_name' => $this->faker->company(),
            'buyer_phone' => $this->faker->phoneNumber(),
            'birds_sold' => $birds,
            'average_weight' => $averageWeight,
            'total_weight' => $totalWeight,
            'rate_per_kg' => $rate,
            'total_amount' => $totalAmount,
            'payment_method' => PaymentMethod::CASH,
            'paid_amount' => $totalAmount,
            'due_amount' => 0,
            'reference_no' => strtoupper($this->faker->bothify('SAL-###')),
            'notes' => null,
            'created_by' => User::factory(),
        ];
    }
}
