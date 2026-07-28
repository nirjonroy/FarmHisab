<?php

namespace App\Http\Requests\MortalityRecord;

use App\Enums\MortalityRecordType;
use App\Models\Batch;
use App\Services\BatchCalculationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMortalityRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('mortality.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => $this->input('type', MortalityRecordType::MORTALITY),
            'cause' => $this->clean('cause'),
            'action_taken' => $this->clean('action_taken'),
            'reported_by' => $this->clean('reported_by'),
            'notes' => $this->clean('notes'),
        ]);
    }

    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'integer', Rule::exists('batches', 'id')->whereNull('deleted_at')],
            'record_date' => ['required', 'date'],
            'type' => ['required', Rule::in(MortalityRecordType::values())],
            'birds' => ['required', 'integer', 'min:1'],
            'cause' => ['nullable', 'string', 'max:150'],
            'action_taken' => ['nullable', 'string', 'max:150'],
            'reported_by' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'batch_id' => __('mortality.batch'),
            'record_date' => __('mortality.record_date'),
            'type' => __('mortality.type'),
            'birds' => __('mortality.birds'),
            'cause' => __('mortality.cause'),
            'action_taken' => __('mortality.action_taken'),
            'reported_by' => __('mortality.reported_by'),
            'notes' => __('mortality.notes'),
        ];
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

            $currentBirds = app(BatchCalculationService::class)->details($batch)['current_birds'];

            if ((int) $this->input('birds', 0) > $currentBirds) {
                $validator->errors()->add('birds', __('mortality.birds_exceed_current'));
            }
        });
    }

    private function clean(string $key): ?string
    {
        $value = $this->input($key);

        if ($value === null) {
            return null;
        }

        $value = trim(preg_replace('/\s+/u', ' ', (string) $value));

        return $value === '' ? null : $value;
    }
}
