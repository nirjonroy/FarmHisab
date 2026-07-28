<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_id',
        'product_id',
        'record_date',
        'feed_name',
        'supplier_name',
        'bags',
        'weight_per_bag',
        'quantity_kg',
        'unit_price_per_bag',
        'total_cost',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'batch_id' => 'integer',
        'product_id' => 'integer',
        'record_date' => 'date',
        'bags' => 'decimal:2',
        'weight_per_bag' => 'decimal:2',
        'quantity_kg' => 'decimal:2',
        'unit_price_per_bag' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'created_by' => 'integer',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
