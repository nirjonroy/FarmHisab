<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_bn',
        'short_name_en',
        'short_name_bn',
        'code',
        'description_en',
        'description_bn',
        'decimal_places',
        'sort_order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'bn') {
            return $this->name_bn ?: $this->name_en ?: $this->code;
        }

        return $this->name_en ?: $this->name_bn ?: $this->code;
    }

    public function getDisplayShortNameAttribute(): string
    {
        if (app()->getLocale() === 'bn') {
            return $this->short_name_bn ?: $this->short_name_en ?: $this->code;
        }

        return $this->short_name_en ?: $this->short_name_bn ?: $this->code;
    }

    public function getDisplayDescriptionAttribute(): ?string
    {
        if (app()->getLocale() === 'bn') {
            return $this->description_bn ?: $this->description_en;
        }

        return $this->description_en ?: $this->description_bn;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')
            ->orderBy('name_en')
            ->orderBy('name_bn')
            ->orderBy('code');
    }
}
