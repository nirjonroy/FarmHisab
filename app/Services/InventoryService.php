<?php

namespace App\Services;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;

class InventoryService
{
    public function currentStock(int $productId, ?int $ignoreMovementId = null): float
    {
        $inbound = $this->sum($productId, InventoryMovementType::inboundValues(), $ignoreMovementId);
        $outbound = $this->sum($productId, InventoryMovementType::outboundValues(), $ignoreMovementId);

        return $inbound - $outbound;
    }

    public function stockValue(int $productId, ?int $ignoreMovementId = null): float
    {
        $inbound = $this->valueSum($productId, InventoryMovementType::inboundValues(), $ignoreMovementId);
        $outbound = $this->valueSum($productId, InventoryMovementType::outboundValues(), $ignoreMovementId);

        return max(0, $inbound - $outbound);
    }

    private function sum(int $productId, array $types, ?int $ignoreMovementId): float
    {
        return (float) InventoryMovement::query()
            ->where('product_id', $productId)
            ->whereIn('type', $types)
            ->when($ignoreMovementId, fn ($query) => $query->whereKeyNot($ignoreMovementId))
            ->sum('quantity');
    }

    private function valueSum(int $productId, array $types, ?int $ignoreMovementId): float
    {
        return (float) InventoryMovement::query()
            ->where('product_id', $productId)
            ->whereIn('type', $types)
            ->when($ignoreMovementId, fn ($query) => $query->whereKeyNot($ignoreMovementId))
            ->sum('total_cost');
    }
}
