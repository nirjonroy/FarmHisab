<?php

namespace App\Repositories;

use App\Models\Batch;
use Illuminate\Database\Eloquent\Builder;

class BatchRepository
{
    public function query(): Builder
    {
        return Batch::query()->with(['farm', 'birdType.parent', 'breed', 'createdBy']);
    }

    public function nextBatchNo(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');
        $prefix = "B-{$year}-";
        $last = Batch::withTrashed()
            ->where('batch_no', 'like', $prefix.'%')
            ->orderByDesc('batch_no')
            ->value('batch_no');

        $next = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
