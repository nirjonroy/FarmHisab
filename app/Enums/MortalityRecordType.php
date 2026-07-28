<?php

namespace App\Enums;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class MortalityRecordType implements CastsAttributes
{
    public const MORTALITY = 'mortality';
    public const CULL = 'cull';

    public string $value;

    public function __construct(?string $value = null)
    {
        $value ??= self::MORTALITY;

        if (! in_array($value, self::values(), true)) {
            throw new InvalidArgumentException("Invalid mortality record type [{$value}].");
        }

        $this->value = $value;
    }

    public function get($model, string $key, $value, array $attributes): self
    {
        return new self($value ?: self::MORTALITY);
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        if ($value instanceof self) {
            return $value->value;
        }

        return (new self($value ?: self::MORTALITY))->value;
    }

    public function label(): string
    {
        return __("mortality.type_{$this->value}");
    }

    public static function values(): array
    {
        return [self::MORTALITY, self::CULL];
    }

    public static function options(): array
    {
        return collect(self::values())
            ->mapWithKeys(fn (string $value) => [$value => (new self($value))->label()])
            ->all();
    }
}
