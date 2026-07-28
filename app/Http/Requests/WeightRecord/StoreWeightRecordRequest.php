<?php

namespace App\Http\Requests\WeightRecord;

use App\Models\Batch;
use App\Services\BatchCalculationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreWeightRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('weights.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $averageWeight = $this->input('average_weight');
        $sampleBirds = $this->input('sample_birds');
        $totalWeight = $this->input('total_weight');

        if (($totalWeight === null || $totalWeight === '') && is_numeric($averageWeight) && is_numeric($sampleBirds)) {
            $totalWeight = (float) $averageWeight * (int) $sampleBirds;
        }

        $this->merge([
            'age_days' => $this->normalizeAgeDays(),
            'total_weight' => $totalWeight,
            'weighed_by' => $this->clean('weighed_by'),
            'notes' => $this->clean('notes'),
        ]);
    }

    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'integer', Rule::exists('batches', 'id')->whereNull('deleted_at')],
            'record_date' => ['required', 'date'],
            'age_days' => ['required', 'integer', 'min:0', 'max:1000'],
            'sample_birds' => ['required', 'integer', 'min:1'],
            'average_weight' => ['required', 'numeric', 'min:0.001', 'max:99999'],
            'total_weight' => ['required', 'numeric', 'min:0.001', 'max:9999999'],
            'target_weight' => ['nullable', 'numeric', 'min:0.001', 'max:99999'],
            'uniformity_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'weighed_by' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'batch_id' => __('weights.batch'),
            'record_date' => __('weights.record_date'),
            'age_days' => __('weights.age_days'),
            'sample_birds' => __('weights.sample_birds'),
            'average_weight' => __('weights.average_weight'),
            'total_weight' => __('weights.total_weight'),
            'target_weight' => __('weights.target_weight'),
            'uniformity_percentage' => __('weights.uniformity_percentage'),
            'weighed_by' => __('weights.weighed_by'),
            'notes' => __('weights.notes'),
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
                $validator->errors()->add('record_date', __('weights.before_batch_purchase'));
            }

            $currentBirds = app(BatchCalculationService::class)->details($batch)['current_birds'];

            if ((int) $this->input('sample_birds', 0) > $currentBirds) {
                $validator->errors()->add('sample_birds', __('weights.sample_exceeds_current'));
            }

            if ($this->duplicateRecordExists($this->duplicateIgnoreId())) {
                $validator->errors()->add('record_date', __('validation.unique', ['attribute' => __('weights.record_date')]));
            }
        });
    }

    protected function duplicateIgnoreId(): ?int
    {
        return null;
    }

    protected function duplicateRecordExists(?int $ignoreId = null): bool
    {
        if (! $this->input('batch_id') || ! $this->input('record_date')) {
            return false;
        }

        return \App\Models\WeightRecord::query()
            ->where('batch_id', $this->input('batch_id'))
            ->whereDate('record_date', $this->input('record_date'))
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }

    protected function normalizeAgeDays(): mixed
    {
        $recordDate = $this->input('record_date');
        $batch = Batch::find($this->input('batch_id'));

        if ($this->filled('age_days') || ! $recordDate || ! $batch) {
            return $this->input('age_days');
        }

        return max(0, $batch->purchase_date->diffInDays($recordDate));
    }

    protected function clean(string $key): ?string
    {
        $value = $this->input($key);

        if ($value === null) {
            return null;
        }

        $value = trim(preg_replace('/\s+/u', ' ', (string) $value));

        return $value === '' ? null : $value;
    }
}
