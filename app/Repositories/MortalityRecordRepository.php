<?php

namespace App\Repositories;

use App\Models\MortalityRecord;
use Illuminate\Database\Eloquent\Builder;

class MortalityRecordRepository
{
    public function query(): Builder
    {
        return MortalityRecord::query()->with(['batch.farm', 'createdBy']);
    }
}
