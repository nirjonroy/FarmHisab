<?php

namespace App\Http\Requests\DailyRecord;

use App\Models\Batch;
use App\Models\DailyRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDailyRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('daily-records.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'notes' => $this->clean('notes'),
            'mortality_birds' => $this->input('mortality_birds', 0),
            'culled_birds' => $this->input('culled_birds', 0),
            'sold_birds' => $this->input('sold_birds', 0),
            'feed_consumed_bags' => $this->input('feed_consumed_bags', 0),
            'feed_cost' => $this->input('feed_cost', 0),
            'medicine_cost' => $this->input('medicine_cost', 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'integer', Rule::exists('batches', 'id')->whereNull('deleted_at')],
            'record_date' => ['required', 'date'],
            'opening_birds' => ['required', 'integer', 'min:0'],
            'mortality_birds' => ['nullable', 'integer', 'min:0'],
            'culled_birds' => ['nullable', 'integer', 'min:0'],
            'sold_birds' => ['nullable', 'integer', 'min:0'],
            'feed_consumed_bags' => ['nullable', 'numeric', 'min:0'],
            'feed_cost' => ['nullable', 'numeric', 'min:0'],
            'medicine_cost' => ['nullable', 'numeric', 'min:0'],
            'average_weight' => ['nullable', 'numeric', 'min:0'],
            'temperature' => ['nullable', 'numeric'],
            'humidity' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'batch_id' => __('daily_records.batch'),
            'record_date' => __('daily_records.record_date'),
            'opening_birds' => __('daily_records.opening_birds'),
            'mortality_birds' => __('daily_records.mortality_birds'),
            'culled_birds' => __('daily_records.culled_birds'),
            'sold_birds' => __('daily_records.sold_birds'),
            'feed_consumed_bags' => __('daily_records.feed_consumed_bags'),
            'feed_cost' => __('daily_records.feed_cost'),
            'medicine_cost' => __('daily_records.medicine_cost'),
            'average_weight' => __('daily_records.average_weight'),
            'temperature' => __('daily_records.temperature'),
            'humidity' => __('daily_records.humidity'),
            'notes' => __('daily_records.notes'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $opening = (int) $this->input('opening_birds', 0);
            $outgoing = (int) $this->input('mortality_birds', 0)
                + (int) $this->input('culled_birds', 0)
                + (int) $this->input('sold_birds', 0);

            if ($outgoing > $opening) {
                $validator->errors()->add('opening_birds', __('daily_records.outgoing_exceeds_opening'));
            }

            $batch = Batch::find($this->input('batch_id'));

            if ($batch && $this->input('record_date') < $batch->purchase_date->format('Y-m-d')) {
                $validator->errors()->add('record_date', __('daily_records.before_batch_purchase'));
            }

            if ($this->duplicateRecordExists()) {
                $validator->errors()->add('record_date', __('validation.unique', ['attribute' => __('daily_records.record_date')]));
            }
        });
    }

    protected function duplicateRecordExists(?int $ignoreId = null): bool
    {
        if (! $this->input('batch_id') || ! $this->input('record_date')) {
            return false;
        }

        return DailyRecord::query()
            ->where('batch_id', $this->input('batch_id'))
            ->whereDate('record_date', $this->input('record_date'))
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
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
