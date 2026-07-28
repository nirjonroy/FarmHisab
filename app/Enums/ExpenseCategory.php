<?php

namespace App\Enums;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ExpenseCategory implements CastsAttributes
{
    public const LABOR = 'labor';
    public const UTILITIES = 'utilities';
    public const TRANSPORT = 'transport';
    public const LITTER = 'litter';
    public const EQUIPMENT = 'equipment';
    public const MAINTENANCE = 'maintenance';
    public const FARM_SUPPLIES = 'farm_supplies';
    public const OTHER = 'other';

    public function __construct(public string $value = self::OTHER)
    {
    }

    public static function values(): array
    {
        return [
            self::LABOR,
            self::UTILITIES,
            self::TRANSPORT,
            self::LITTER,
            self::EQUIPMENT,
            self::MAINTENANCE,
            self::FARM_SUPPLIES,
            self::OTHER,
        ];
    }

    public static function options(): array
    {
        return collect(self::values())->mapWithKeys(fn (string $value) => [$value => (new self($value))->label()])->all();
    }

    public function label(): string
    {
        return __("expenses.category_{$this->value}");
    }

    public function get($model, string $key, $value, array $attributes): self
    {
        return new self($value ?: self::OTHER);
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        return $value instanceof self ? $value->value : (string) $value;
    }
}
