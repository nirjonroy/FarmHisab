<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_id',
        'sale_date',
        'buyer_name',
        'buyer_phone',
        'birds_sold',
        'average_weight',
        'total_weight',
        'rate_per_kg',
        'total_amount',
        'payment_method',
        'paid_amount',
        'due_amount',
        'reference_no',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'batch_id' => 'integer',
        'created_by' => 'integer',
        'sale_date' => 'date',
        'birds_sold' => 'integer',
        'average_weight' => 'decimal:3',
        'total_weight' => 'decimal:3',
        'rate_per_kg' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'payment_method' => PaymentMethod::class,
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
        return $query->latest('sale_date')->latest('id');
    }
}
