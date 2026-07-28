<?php

namespace App\Models;

use App\Enums\InventoryMovementType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'batch_id',
        'movement_date',
        'type',
        'quantity',
        'unit_cost',
        'total_cost',
        'supplier_name',
        'reference_no',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'batch_id' => 'integer',
        'created_by' => 'integer',
        'movement_date' => 'date',
        'type' => InventoryMovementType::class,
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

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
        return $query->latest('movement_date')->latest('id');
    }
}
