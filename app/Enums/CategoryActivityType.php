<?php

namespace App\Enums;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class CategoryActivityType implements CastsAttributes
{
    public const PRODUCTION = 'production';
    public const TRADING = 'trading';
    public const HYBRID = 'hybrid';

    public string $value;

    public function __construct(?string $value = null)
    {
        $value ??= self::PRODUCTION;

        if (! in_array($value, self::values(), true)) {
            throw new InvalidArgumentException("Invalid category activity type [{$value}].");
        }

        $this->value = $value;
    }

    public function get($model, string $key, $value, array $attributes): self
    {
        return new self($value ?: self::PRODUCTION);
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        if ($value instanceof self) {
            return $value->value;
        }

        return (new self($value ?: self::PRODUCTION))->value;
    }

    public function label(): string
    {
        return __("farm_categories.{$this->value}");
    }

    public function is(string $value): bool
    {
        return $this->value === $value;
    }

    public static function tryFrom(?string $value): ?self
    {
        return in_array($value, self::values(), true) ? new self($value) : null;
    }

    public static function values(): array
    {
        return [
            self::PRODUCTION,
            self::TRADING,
            self::HYBRID,
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
