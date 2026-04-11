<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitchenUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'date', 'notes', 'students_fed',
    ];

    protected $casts = [
        'date' => 'date',
        'students_fed' => 'integer',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(KitchenUsageItem::class, 'usage_id');
    }
}
