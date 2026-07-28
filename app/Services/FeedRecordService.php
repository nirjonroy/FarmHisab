<?php

namespace App\Services;

class FeedRecordService
{
    public function payload(array $data): array
    {
        $bags = (float) ($data['bags'] ?? 0);
        $weightPerBag = (float) ($data['weight_per_bag'] ?? 0);
        $price = (float) ($data['unit_price_per_bag'] ?? 0);

        $data['quantity_kg'] = round($bags * $weightPerBag, 2);
        $data['total_cost'] = round($bags * $price, 2);

        return $data;
    }
}
