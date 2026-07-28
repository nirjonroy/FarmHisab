<?php

namespace Database\Factories;

use App\Enums\BatchStatus;
use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Batch>
 */
class BatchFactory extends Factory
{
    public function definition()
    {
        $parent = FarmCategory::factory()->create(['parent_id' => null, 'name_en' => 'Poultry']);
        $birdType = FarmCategory::factory()->create(['parent_id' => $parent->id, 'name_en' => 'Broiler']);
        $breed = FarmVariety::factory()->create(['farm_category_id' => $birdType->id, 'name_en' => 'Cobb 500']);
        $initialBirds = $this->faker->numberBetween(500, 2500);
        $price = $this->faker->randomFloat(2, 60, 120);

        return [
            'batch_no' => 'B-'.now()->year.'-'.$this->faker->unique()->numberBetween(100, 999),
            'batch_name' => 'Batch '.$this->faker->unique()->word(),
            'farm_id' => Farm::factory(),
            'bird_type_id' => $birdType->id,
            'breed_id' => $breed->id,
            'supplier_name' => $this->faker->company(),
            'purchase_date' => now()->toDateString(),
            'arrival_date' => now()->addDay()->toDateString(),
            'initial_birds' => $initialBirds,
            'purchase_price_per_bird' => $price,
            'total_purchase_cost' => $initialBirds * $price,
            'expected_market_weight' => 2.2,
            'expected_market_age' => 35,
            'feed_target_bags' => 120,
            'medicine_budget' => 5000,
            'other_budget' => 2000,
            'notes' => null,
            'status' => BatchStatus::ACTIVE,
            'created_by' => User::factory(),
        ];
    }
}
