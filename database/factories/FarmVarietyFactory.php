<?php

namespace Database\Factories;

use App\Models\FarmCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FarmVariety>
 */
class FarmVarietyFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'farm_category_id' => FarmCategory::factory(),
            'name_en' => Str::title($name),
            'name_bn' => null,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(100, 999),
            'code' => strtoupper($this->faker->unique()->bothify('VAR-###')),
            'description_en' => $this->faker->sentence(),
            'description_bn' => null,
            'sort_order' => $this->faker->numberBetween(0, 20),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
