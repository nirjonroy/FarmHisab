<?php

namespace App\Http\Requests\Sale;

class UpdateSaleRequest extends StoreSaleRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sales.manage') ?? false;
    }

    protected function existingBirdsAllowance(): int
    {
        return (int) ($this->route('sale')?->birds_sold ?? 0);
    }
}
