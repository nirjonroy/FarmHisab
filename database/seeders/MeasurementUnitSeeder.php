<?php

namespace Database\Seeders;

use App\Models\MeasurementUnit;
use Illuminate\Database\Seeder;

class MeasurementUnitSeeder extends Seeder
{
    private const UNITS = [
        ['name_en' => 'Kilogram', 'name_bn' => 'কিলোগ্রাম', 'short_name_en' => 'kg', 'short_name_bn' => 'কেজি', 'code' => 'kg', 'decimal_places' => 2, 'sort_order' => 10],
        ['name_en' => 'Gram', 'name_bn' => 'গ্রাম', 'short_name_en' => 'g', 'short_name_bn' => 'গ্রাম', 'code' => 'gram', 'decimal_places' => 2, 'sort_order' => 20],
        ['name_en' => 'Maund', 'name_bn' => 'মণ', 'short_name_en' => 'maund', 'short_name_bn' => 'মণ', 'code' => 'maund', 'decimal_places' => 2, 'sort_order' => 30],
        ['name_en' => 'Metric Ton', 'name_bn' => 'মেট্রিক টন', 'short_name_en' => 'ton', 'short_name_bn' => 'টন', 'code' => 'ton', 'decimal_places' => 3, 'sort_order' => 40],
        ['name_en' => 'Litre', 'name_bn' => 'লিটার', 'short_name_en' => 'L', 'short_name_bn' => 'লিটার', 'code' => 'litre', 'decimal_places' => 2, 'sort_order' => 50],
        ['name_en' => 'Millilitre', 'name_bn' => 'মিলিলিটার', 'short_name_en' => 'ml', 'short_name_bn' => 'মি.লি.', 'code' => 'millilitre', 'decimal_places' => 2, 'sort_order' => 60],
        ['name_en' => 'Bag', 'name_bn' => 'বস্তা', 'short_name_en' => 'bag', 'short_name_bn' => 'বস্তা', 'code' => 'bag', 'decimal_places' => 0, 'sort_order' => 70],
        ['name_en' => 'Packet', 'name_bn' => 'প্যাকেট', 'short_name_en' => 'pkt', 'short_name_bn' => 'প্যাকেট', 'code' => 'packet', 'decimal_places' => 0, 'sort_order' => 80],
        ['name_en' => 'Piece', 'name_bn' => 'পিস', 'short_name_en' => 'pcs', 'short_name_bn' => 'পিস', 'code' => 'piece', 'decimal_places' => 0, 'sort_order' => 90],
        ['name_en' => 'Dozen', 'name_bn' => 'ডজন', 'short_name_en' => 'dozen', 'short_name_bn' => 'ডজন', 'code' => 'dozen', 'decimal_places' => 0, 'sort_order' => 100],
        ['name_en' => 'Bundle', 'name_bn' => 'বান্ডিল', 'short_name_en' => 'bundle', 'short_name_bn' => 'বান্ডিল', 'code' => 'bundle', 'decimal_places' => 0, 'sort_order' => 110],
        ['name_en' => 'Bottle', 'name_bn' => 'বোতল', 'short_name_en' => 'btl', 'short_name_bn' => 'বোতল', 'code' => 'bottle', 'decimal_places' => 0, 'sort_order' => 120],
    ];

    public function run(): void
    {
        foreach (self::UNITS as $unit) {
            MeasurementUnit::updateOrCreate(
                ['code' => $unit['code']],
                $unit + [
                    'description_en' => null,
                    'description_bn' => null,
                    'is_active' => true,
                    'created_by' => null,
                ]
            );
        }
    }
}
