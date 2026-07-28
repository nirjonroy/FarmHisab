<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\DailyRecord;

class DailyRecordService
{
    public function defaultOpeningBirds(Batch $batch, ?DailyRecord $ignore = null): int
    {
        $query = $batch->dailyRecords()->ordered();

        if ($ignore) {
            $query->whereKeyNot($ignore->id);
        }

        $previous = $query->first();

        return $previous ? $previous->closing_birds : $batch->initial_birds;
    }

    public function payload(array $data): array
    {
        $data['opening_birds'] = (int) $data['opening_birds'];
        $data['mortality_birds'] = (int) ($data['mortality_birds'] ?? 0);
        $data['culled_birds'] = (int) ($data['culled_birds'] ?? 0);
        $data['sold_birds'] = (int) ($data['sold_birds'] ?? 0);
        $data['closing_birds'] = max(
            0,
            $data['opening_birds'] - $data['mortality_birds'] - $data['culled_birds'] - $data['sold_birds']
        );

        return $data;
    }
}
