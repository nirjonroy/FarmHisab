<?php

namespace App\Repositories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;

class SaleRepository
{
    public function query(): Builder
    {
        return Sale::query()->with(['batch.farm', 'createdBy']);
    }
}
