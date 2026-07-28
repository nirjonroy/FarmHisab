<?php

namespace App\Repositories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;

class ExpenseRepository
{
    public function query(): Builder
    {
        return Expense::query()->with(['batch.farm', 'createdBy']);
    }
}
