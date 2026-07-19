<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Farm>
 */
class FarmFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->company().' Farm',
            'code' => strtoupper($this->faker->unique()->bothify('FARM-###')),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'district' => $this->faker->city(),
            'upazila' => $this->faker->citySuffix(),
            'union_name' => $this->faker->streetName(),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
