<?php

namespace App\Repositories;

use App\Models\WeightRecord;
use Illuminate\Database\Eloquent\Builder;

class WeightRecordRepository
{
    public function query(): Builder
    {
        return WeightRecord::query()->with(['batch.farm', 'createdBy']);
    }
}
