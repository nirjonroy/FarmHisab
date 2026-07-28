<?php

namespace App\Http\Requests\WeightRecord;

class UpdateWeightRecordRequest extends StoreWeightRecordRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('weights.update') ?? false;
    }

    protected function duplicateIgnoreId(): ?int
    {
        return $this->route('weightRecord')?->id;
    }
}
