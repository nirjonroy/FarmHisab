<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmVariety extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_category_id',
        'name_en',
        'name_bn',
        'slug',
        'code',
        'description_en',
        'description_bn',
        'sort_order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FarmCategory::class, 'farm_category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'bn') {
            return $this->name_bn ?: $this->name_en ?: $this->slug;
        }

        return $this->name_en ?: $this->name_bn ?: $this->slug;
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
            ->orderBy('slug');
    }
}
