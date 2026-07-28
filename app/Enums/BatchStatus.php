<?php

namespace App\Enums;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class BatchStatus implements CastsAttributes
{
    public const ACTIVE = 'active';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public string $value;

    public function __construct(?string $value = null)
    {
        $value ??= self::ACTIVE;

        if (! in_array($value, self::values(), true)) {
            throw new InvalidArgumentException("Invalid batch status [{$value}].");
        }

        $this->value = $value;
    }

    public function get($model, string $key, $value, array $attributes): self
    {
        return new self($value ?: self::ACTIVE);
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        if ($value instanceof self) {
            return $value->value;
        }

        return (new self($value ?: self::ACTIVE))->value;
    }

    public function label(): string
    {
        return __("batches.status_{$this->value}");
    }

    public static function tryFrom(?string $value): ?self
    {
        return in_array($value, self::values(), true) ? new self($value) : null;
    }

    public static function values(): array
    {
        return [
            self::ACTIVE,
            self::COMPLETED,
            self::CANCELLED,
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
