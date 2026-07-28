<?php

namespace App\Http\Requests\DailyRecord;

class UpdateDailyRecordRequest extends StoreDailyRecordRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('daily-records.update') ?? false;
    }

    protected function duplicateRecordExists(?int $ignoreId = null): bool
    {
        $dailyRecord = $this->route('daily_record');

        return parent::duplicateRecordExists($dailyRecord?->id);
    }
}
