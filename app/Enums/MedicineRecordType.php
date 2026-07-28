<?php

namespace App\Enums;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class MedicineRecordType implements CastsAttributes
{
    public const MEDICINE = 'medicine';
    public const VACCINE = 'vaccine';

    public string $value;

    public function __construct(?string $value = null)
    {
        $value ??= self::MEDICINE;

        if (! in_array($value, self::values(), true)) {
            throw new InvalidArgumentException("Invalid medicine record type [{$value}].");
        }

        $this->value = $value;
    }

    public function get($model, string $key, $value, array $attributes): self
    {
        return new self($value ?: self::MEDICINE);
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        if ($value instanceof self) {
            return $value->value;
        }

        return (new self($value ?: self::MEDICINE))->value;
    }

    public function label(): string
    {
        return __("medicine.type_{$this->value}");
    }

    public static function values(): array
    {
        return [self::MEDICINE, self::VACCINE];
    }

    public static function options(): array
    {
        return collect(self::values())
            ->mapWithKeys(fn (string $value) => [$value => (new self($value))->label()])
            ->all();
    }
}
