<?php

namespace Database\Factories;

use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shed>
 */
class ShedFactory extends Factory
{
    public function definition()
    {
        return [
            'farm_id' => Farm::factory(),
            'name' => $this->faker->word().' Shed',
            'code' => strtoupper($this->faker->unique()->bothify('SHED-###')),
            'capacity' => $this->faker->numberBetween(100, 5000),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
