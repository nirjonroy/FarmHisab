<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineRecord extends Model
{
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}
