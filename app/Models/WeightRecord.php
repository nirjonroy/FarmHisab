<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeightRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_id',
        'record_date',
        'age_days',
        'sample_birds',
        'average_weight',
        'total_weight',
        'target_weight',
        'uniformity_percentage',
        'weighed_by',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'batch_id' => 'integer',
        'created_by' => 'integer',
        'record_date' => 'date',
        'age_days' => 'integer',
        'sample_birds' => 'integer',
        'average_weight' => 'decimal:3',
        'total_weight' => 'decimal:3',
        'target_weight' => 'decimal:3',
        'uniformity_percentage' => 'decimal:2',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->latest('record_date')->latest('id');
    }
}
