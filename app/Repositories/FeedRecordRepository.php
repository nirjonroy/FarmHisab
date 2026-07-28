<?php

namespace App\Repositories;

use App\Models\FeedRecord;
use Illuminate\Database\Eloquent\Builder;

class FeedRecordRepository
{
    public function query(): Builder
    {
        return FeedRecord::query()->with(['batch.farm', 'product', 'createdBy']);
    }
}
