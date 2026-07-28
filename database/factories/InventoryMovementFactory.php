<?php

namespace Database\Factories;

use App\Enums\InventoryMovementType;
use App\Models\Batch;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryMovementFactory extends Factory
{
    public function definition()
    {
        $quantity = $this->faker->randomFloat(3, 1, 100);
        $unitCost = $this->faker->randomFloat(2, 50, 3000);

        return [
            'product_id' => Product::factory(),
            'batch_id' => Batch::factory(),
            'movement_date' => now()->toDateString(),
            'type' => InventoryMovementType::PURCHASE,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $quantity * $unitCost,
            'supplier_name' => $this->faker->company(),
            'reference_no' => strtoupper($this->faker->bothify('INV-###')),
            'notes' => null,
            'created_by' => User::factory(),
        ];
    }
}
