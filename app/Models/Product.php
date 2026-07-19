<?php

namespace App\Models;

use App\Enums\ProductUsageType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_category_id',
        'measurement_unit_id',
        'name_en',
        'name_bn',
        'sku',
        'barcode',
        'usage_type',
        'description_en',
        'description_bn',
        'sort_order',
        'is_stock_tracked',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'usage_type' => ProductUsageType::class,
        'sort_order' => 'integer',
        'is_stock_tracked' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FarmCategory::class, 'farm_category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'measurement_unit_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'bn') {
            return $this->name_bn ?: $this->name_en ?: $this->sku;
        }

        return $this->name_en ?: $this->name_bn ?: $this->sku;
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

    public function scopeStockTracked(Builder $query): Builder
    {
        return $query->where('is_stock_tracked', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')
            ->orderBy('name_en')
            ->orderBy('name_bn')
            ->orderBy('sku');
    }
}
