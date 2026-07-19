<?php

namespace App\Enums;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class ProductUsageType implements CastsAttributes
{
    public const INPUT = 'input';
    public const OUTPUT = 'output';
    public const BOTH = 'both';

    public string $value;

    public function __construct(?string $value = null)
    {
        $value ??= self::BOTH;

        if (! in_array($value, self::values(), true)) {
            throw new InvalidArgumentException("Invalid product usage type [{$value}].");
        }

        $this->value = $value;
    }

    public function get($model, string $key, $value, array $attributes): self
    {
        return new self($value ?: self::BOTH);
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        if ($value instanceof self) {
            return $value->value;
        }

        return (new self($value ?: self::BOTH))->value;
    }

    public function label(): string
    {
        return __("products.{$this->value}");
    }

    public static function tryFrom(?string $value): ?self
    {
        return in_array($value, self::values(), true) ? new self($value) : null;
    }

    public static function values(): array
    {
        return [
            self::INPUT,
            self::OUTPUT,
            self::BOTH,
        ];
    }

    public static function options(): array
    {
        return collect(self::values())
            ->mapWithKeys(fn (string $value) => [$value => (new self($value))->label()])
            ->all();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
