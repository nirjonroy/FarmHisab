<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\FeedRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeedRecord>
 */
class FeedRecordFactory extends Factory
{
    public function definition()
    {
        $bags = $this->faker->randomFloat(2, 1, 20);
        $weight = 50;
        $price = $this->faker->randomFloat(2, 1800, 3200);

        return [
            'batch_id' => Batch::factory(),
            'product_id' => null,
            'record_date' => now()->toDateString(),
            'feed_name' => 'Starter Feed',
            'supplier_name' => $this->faker->company(),
            'bags' => $bags,
            'weight_per_bag' => $weight,
            'quantity_kg' => $bags * $weight,
            'unit_price_per_bag' => $price,
            'total_cost' => $bags * $price,
            'notes' => null,
            'created_by' => User::factory(),
        ];
    }
}
