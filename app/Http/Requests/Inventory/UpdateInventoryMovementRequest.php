<?php

namespace App\Http\Requests\Inventory;

class UpdateInventoryMovementRequest extends StoreInventoryMovementRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('inventory.manage') ?? false;
    }

    protected function ignoreMovementId(): ?int
    {
        return $this->route('inventoryMovement')?->id;
    }
}
