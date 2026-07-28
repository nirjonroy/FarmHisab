<?php

namespace App\Repositories;

use App\Models\InventoryMovement;
use Illuminate\Database\Eloquent\Builder;

class InventoryMovementRepository
{
    public function query(): Builder
    {
        return InventoryMovement::query()->with(['product.unit', 'batch.farm', 'createdBy']);
    }
}
