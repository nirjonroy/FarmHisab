<?php

namespace Database\Factories;

use App\Enums\ExpenseCategory;
use App\Enums\PaymentMethod;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition()
    {
        return [
            'batch_id' => Batch::factory(),
            'expense_date' => now()->toDateString(),
            'category' => $this->faker->randomElement(ExpenseCategory::values()),
            'title' => 'Farm expense',
            'payee' => $this->faker->company(),
            'amount' => $this->faker->randomFloat(2, 500, 5000),
            'payment_method' => PaymentMethod::CASH,
            'reference_no' => strtoupper($this->faker->bothify('EXP-###')),
            'notes' => null,
            'created_by' => User::factory(),
        ];
    }
}
