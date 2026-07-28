<?php

namespace App\Repositories;

use App\Models\DailyRecord;
use Illuminate\Database\Eloquent\Builder;

class DailyRecordRepository
{
    public function query(): Builder
    {
        return DailyRecord::query()->with(['batch.farm', 'createdBy']);
    }
}
