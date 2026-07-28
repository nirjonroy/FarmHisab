<?php

namespace App\Enums;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class InventoryMovementType implements CastsAttributes
{
    public const PURCHASE = 'purchase';
    public const ADJUSTMENT_IN = 'adjustment_in';
    public const RETURN_IN = 'return_in';
    public const USAGE = 'usage';
    public const DAMAGE = 'damage';
    public const SALE_OUT = 'sale_out';
    public const ADJUSTMENT_OUT = 'adjustment_out';

    public function __construct(public string $value = self::PURCHASE)
    {
    }

    public static function values(): array
    {
        return [
            self::PURCHASE,
            self::ADJUSTMENT_IN,
            self::RETURN_IN,
            self::USAGE,
            self::DAMAGE,
            self::SALE_OUT,
            self::ADJUSTMENT_OUT,
        ];
    }

    public static function inboundValues(): array
    {
        return [self::PURCHASE, self::ADJUSTMENT_IN, self::RETURN_IN];
    }

    public static function outboundValues(): array
    {
        return [self::USAGE, self::DAMAGE, self::SALE_OUT, self::ADJUSTMENT_OUT];
    }

    public static function options(): array
    {
        return collect(self::values())->mapWithKeys(fn (string $value) => [$value => (new self($value))->label()])->all();
    }

    public function label(): string
    {
        return __("inventory.type_{$this->value}");
    }

    public function direction(): string
    {
        return in_array($this->value, self::inboundValues(), true) ? 'in' : 'out';
    }

    public function get($model, string $key, $value, array $attributes): self
    {
        return new self($value ?: self::PURCHASE);
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        return $value instanceof self ? $value->value : (string) $value;
    }
}
