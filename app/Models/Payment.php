<?php

namespace App\Models;

use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([SchoolScope::class])]
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'guardian_id',
        'reference',
        'total_amount',
        'platform_fee',
        'school_amount',
        'status',
        'payment_method',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'school_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function getPlatformFeePercentageAttribute(): float
    {
        if ($this->school_amount > 0) {
            return round(($this->platform_fee / ($this->school_amount + $this->platform_fee)) * 100, 2);
        }
        return 0;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'green',
            'pending' => 'yellow',
            'failed' => 'red',
            'refunded' => 'gray',
            default => 'gray',
        };
    }
}
