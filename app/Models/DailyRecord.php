<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_id',
        'record_date',
        'opening_birds',
        'mortality_birds',
        'culled_birds',
        'sold_birds',
        'closing_birds',
        'feed_consumed_bags',
        'feed_cost',
        'medicine_cost',
        'average_weight',
        'temperature',
        'humidity',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'batch_id' => 'integer',
        'record_date' => 'date',
        'opening_birds' => 'integer',
        'mortality_birds' => 'integer',
        'culled_birds' => 'integer',
        'sold_birds' => 'integer',
        'closing_birds' => 'integer',
        'feed_consumed_bags' => 'decimal:2',
        'feed_cost' => 'decimal:2',
        'medicine_cost' => 'decimal:2',
        'average_weight' => 'decimal:3',
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'created_by' => 'integer',
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
