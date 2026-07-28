<?php

namespace App\Models;

use App\Enums\MedicineRecordType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicineRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_id',
        'product_id',
        'record_date',
        'type',
        'medicine_name',
        'supplier_name',
        'dosage',
        'purpose',
        'quantity',
        'unit',
        'unit_price',
        'total_cost',
        'next_due_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'batch_id' => 'integer',
        'product_id' => 'integer',
        'record_date' => 'date',
        'type' => MedicineRecordType::class,
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'next_due_date' => 'date',
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
