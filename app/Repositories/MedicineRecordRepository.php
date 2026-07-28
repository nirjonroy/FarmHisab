<?php

namespace App\Repositories;

use App\Models\MedicineRecord;
use Illuminate\Database\Eloquent\Builder;

class MedicineRecordRepository
{
    public function query(): Builder
    {
        return MedicineRecord::query()->with(['batch.farm', 'product', 'createdBy']);
    }
}
