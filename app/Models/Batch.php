<?php

namespace App\Models;

use App\Enums\BatchStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_no',
        'batch_name',
        'farm_id',
        'bird_type_id',
        'breed_id',
        'supplier_name',
        'purchase_date',
        'arrival_date',
        'initial_birds',
        'purchase_price_per_bird',
        'total_purchase_cost',
        'expected_market_weight',
        'expected_market_age',
        'feed_target_bags',
        'medicine_budget',
        'other_budget',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'farm_id' => 'integer',
        'bird_type_id' => 'integer',
        'breed_id' => 'integer',
        'created_by' => 'integer',
        'purchase_date' => 'date',
        'arrival_date' => 'date',
        'initial_birds' => 'integer',
        'purchase_price_per_bird' => 'decimal:2',
        'total_purchase_cost' => 'decimal:2',
        'expected_market_weight' => 'decimal:3',
        'expected_market_age' => 'integer',
        'feed_target_bags' => 'decimal:2',
        'medicine_budget' => 'decimal:2',
        'other_budget' => 'decimal:2',
        'status' => BatchStatus::class,
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function birdType(): BelongsTo
    {
        return $this->belongsTo(FarmCategory::class, 'bird_type_id');
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(FarmVariety::class, 'breed_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function feedRecords(): HasMany
    {
        return $this->hasMany(FeedRecord::class);
    }

    public function dailyRecords(): HasMany
    {
        return $this->hasMany(DailyRecord::class);
    }

    public function medicineRecords(): HasMany
    {
        return $this->hasMany(MedicineRecord::class);
    }

    public function mortalityRecords(): HasMany
    {
        return $this->hasMany(MortalityRecord::class);
    }

    public function weightRecords(): HasMany
    {
        return $this->hasMany(WeightRecord::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', BatchStatus::ACTIVE);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->latest('purchase_date')->latest('id');
    }
}
