<?php

namespace App\Http\Requests\MedicineRecord;

use App\Enums\MedicineRecordType;
use App\Models\Batch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMedicineRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()?->can('medicine.manage') || $this->user()?->can('vaccinations.manage')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => $this->input('type', MedicineRecordType::MEDICINE),
            'medicine_name' => $this->clean('medicine_name'),
            'supplier_name' => $this->clean('supplier_name'),
            'dosage' => $this->clean('dosage'),
            'purpose' => $this->clean('purpose'),
            'unit' => $this->clean('unit'),
            'notes' => $this->clean('notes'),
            'quantity' => $this->input('quantity', 0),
            'unit_price' => $this->input('unit_price', 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'integer', Rule::exists('batches', 'id')->whereNull('deleted_at')],
            'product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'record_date' => ['required', 'date'],
            'type' => ['required', Rule::in(MedicineRecordType::values())],
            'medicine_name' => ['nullable', 'string', 'max:150'],
            'supplier_name' => ['nullable', 'string', 'max:150'],
            'dosage' => ['nullable', 'string', 'max:150'],
            'purpose' => ['nullable', 'string', 'max:150'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['nullable', 'string', 'max:50'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'next_due_date' => ['nullable', 'date', 'after_or_equal:record_date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'batch_id' => __('medicine.batch'),
            'product_id' => __('medicine.product'),
            'record_date' => __('medicine.record_date'),
            'type' => __('medicine.type'),
            'medicine_name' => __('medicine.medicine_name'),
            'supplier_name' => __('medicine.supplier_name'),
            'dosage' => __('medicine.dosage'),
            'purpose' => __('medicine.purpose'),
            'quantity' => __('medicine.quantity'),
            'unit' => __('medicine.unit'),
            'unit_price' => __('medicine.unit_price'),
            'next_due_date' => __('medicine.next_due_date'),
            'notes' => __('medicine.notes'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $batch = Batch::find($this->input('batch_id'));

            if ($batch && $this->input('record_date') < $batch->purchase_date->format('Y-m-d')) {
                $validator->errors()->add('record_date', __('medicine.before_batch_purchase'));
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
