<?php

namespace Database\Factories;

use App\Enums\MortalityRecordType;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MortalityRecordFactory extends Factory
{
    public function definition()
    {
        return [
            'batch_id' => Batch::factory(),
            'record_date' => now()->toDateString(),
            'type' => MortalityRecordType::MORTALITY,
            'birds' => $this->faker->numberBetween(1, 10),
            'cause' => 'Heat stress',
            'action_taken' => 'Improved ventilation',
            'reported_by' => $this->faker->name(),
            'notes' => null,
            'created_by' => User::factory(),
        ];
    }
}
