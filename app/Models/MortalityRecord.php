<?php

namespace App\Models;

use App\Enums\MortalityRecordType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MortalityRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_id',
        'record_date',
        'type',
        'birds',
        'cause',
        'action_taken',
        'reported_by',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'batch_id' => 'integer',
        'record_date' => 'date',
        'type' => MortalityRecordType::class,
        'birds' => 'integer',
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
