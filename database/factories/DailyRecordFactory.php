<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyRecord>
 */
class DailyRecordFactory extends Factory
{
    public function definition()
    {
        $opening = $this->faker->numberBetween(500, 1500);
        $mortality = $this->faker->numberBetween(0, 10);
        $culled = $this->faker->numberBetween(0, 5);
        $sold = $this->faker->numberBetween(0, 20);

        return [
            'batch_id' => Batch::factory(),
            'record_date' => now()->toDateString(),
            'opening_birds' => $opening,
            'mortality_birds' => $mortality,
            'culled_birds' => $culled,
            'sold_birds' => $sold,
            'closing_birds' => $opening - $mortality - $culled - $sold,
            'feed_consumed_bags' => $this->faker->randomFloat(2, 5, 25),
            'feed_cost' => $this->faker->randomFloat(2, 1000, 5000),
            'medicine_cost' => $this->faker->randomFloat(2, 100, 800),
            'average_weight' => $this->faker->randomFloat(3, 0.5, 2.5),
            'temperature' => $this->faker->randomFloat(2, 25, 35),
            'humidity' => $this->faker->randomFloat(2, 40, 80),
            'notes' => null,
            'created_by' => User::factory(),
        ];
    }
}
