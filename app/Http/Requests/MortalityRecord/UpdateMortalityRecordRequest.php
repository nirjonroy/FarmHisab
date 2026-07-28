<?php

namespace App\Http\Requests\MortalityRecord;

use App\Models\Batch;
use App\Services\BatchCalculationService;
use Illuminate\Validation\Validator;

class UpdateMortalityRecordRequest extends StoreMortalityRecordRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('mortality.update') ?? false;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $batch = Batch::find($this->input('batch_id'));

            if (! $batch) {
                return;
            }

            if ($this->input('record_date') < $batch->purchase_date->format('Y-m-d')) {
                $validator->errors()->add('record_date', __('mortality.before_batch_purchase'));
            }

            $record = $this->route('mortalityRecord');
            $currentBirds = app(BatchCalculationService::class)->details($batch)['current_birds'] + (int) ($record?->birds ?? 0);

            if ((int) $this->input('birds', 0) > $currentBirds) {
                $validator->errors()->add('birds', __('mortality.birds_exceed_current'));
            }
        });
    }
}
