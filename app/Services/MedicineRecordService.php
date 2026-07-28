<?php

namespace App\Services;

class MedicineRecordService
{
    public function payload(array $data): array
    {
        $quantity = (float) ($data['quantity'] ?? 0);
        $price = (float) ($data['unit_price'] ?? 0);

        $data['total_cost'] = round($quantity * $price, 2);

        return $data;
    }
}
