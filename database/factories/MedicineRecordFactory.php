<?php

namespace Database\Factories;

use App\Enums\MedicineRecordType;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineRecordFactory extends Factory
{
    public function definition()
    {
        $quantity = $this->faker->randomFloat(2, 1, 10);
        $price = $this->faker->randomFloat(2, 100, 600);

        return [
            'batch_id' => Batch::factory(),
            'product_id' => null,
            'record_date' => now()->toDateString(),
            'type' => MedicineRecordType::MEDICINE,
            'medicine_name' => 'Vitamin Mix',
            'supplier_name' => $this->faker->company(),
            'dosage' => '1 ml per liter',
            'purpose' => 'General health',
            'quantity' => $quantity,
            'unit' => 'ml',
            'unit_price' => $price,
            'total_cost' => $quantity * $price,
            'next_due_date' => null,
            'notes' => null,
            'created_by' => User::factory(),
        ];
    }
}
