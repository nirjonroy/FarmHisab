<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FarmCategory>
 */
class FarmCategoryFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'parent_id' => null,
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(100, 999),
            'description' => $this->faker->sentence(),
            'icon' => null,
            'sort_order' => $this->faker->numberBetween(0, 20),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
