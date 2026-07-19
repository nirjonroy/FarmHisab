<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MeasurementUnit>
 */
class MeasurementUnitFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->unique()->word();

        return [
            'name_en' => Str::title($name),
            'name_bn' => null,
            'short_name_en' => Str::lower(Str::limit($name, 5, '')),
            'short_name_bn' => null,
            'code' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(100, 999),
            'description_en' => null,
            'description_bn' => null,
            'decimal_places' => $this->faker->numberBetween(0, 4),
            'sort_order' => $this->faker->numberBetween(0, 20),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
