<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeightRecordFactory extends Factory
{
    public function definition()
    {
        $sampleBirds = $this->faker->numberBetween(20, 100);
        $averageWeight = $this->faker->randomFloat(3, 0.4, 2.5);

        return [
            'batch_id' => Batch::factory(),
            'record_date' => now()->toDateString(),
            'age_days' => $this->faker->numberBetween(7, 45),
            'sample_birds' => $sampleBirds,
            'average_weight' => $averageWeight,
            'total_weight' => $sampleBirds * $averageWeight,
            'target_weight' => $this->faker->randomFloat(3, 0.5, 2.8),
            'uniformity_percentage' => $this->faker->randomFloat(2, 70, 95),
            'weighed_by' => $this->faker->name(),
            'notes' => null,
            'created_by' => User::factory(),
        ];
    }
}
