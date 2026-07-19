<?php

namespace Database\Factories;

use App\Enums\ProductUsageType;
use App\Models\FarmCategory;
use App\Models\MeasurementUnit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'farm_category_id' => FarmCategory::factory(),
            'measurement_unit_id' => MeasurementUnit::factory(),
            'name_en' => Str::title($name),
            'name_bn' => null,
            'sku' => Str::upper(Str::slug($name)).'-'.$this->faker->unique()->numberBetween(100, 999),
            'barcode' => null,
            'usage_type' => ProductUsageType::BOTH,
            'description_en' => null,
            'description_bn' => null,
            'sort_order' => $this->faker->numberBetween(0, 20),
            'is_stock_tracked' => true,
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
