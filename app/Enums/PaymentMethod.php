<?php

namespace App\Enums;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class PaymentMethod implements CastsAttributes
{
    public const CASH = 'cash';
    public const BANK = 'bank';
    public const MOBILE_BANKING = 'mobile_banking';
    public const DUE = 'due';
    public const OTHER = 'other';

    public function __construct(public string $value = self::CASH)
    {
    }

    public static function values(): array
    {
        return [
            self::CASH,
            self::BANK,
            self::MOBILE_BANKING,
            self::DUE,
            self::OTHER,
        ];
    }

    public static function options(): array
    {
        return collect(self::values())->mapWithKeys(fn (string $value) => [$value => (new self($value))->label()])->all();
    }

    public function label(): string
    {
        return __("expenses.payment_{$this->value}");
    }

    public function get($model, string $key, $value, array $attributes): self
    {
        return new self($value ?: self::CASH);
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        return $value instanceof self ? $value->value : (string) $value;
    }
}
