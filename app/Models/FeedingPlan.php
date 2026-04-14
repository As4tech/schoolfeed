<?php

namespace App\Models;

use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ScopedBy([SchoolScope::class])]
class FeedingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'type',
        'price',
        'duration_days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_plan')
            ->withPivot(['start_date', 'end_date', 'status', 'amount_paid', 'notes'])
            ->withTimestamps();
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'GH₵' . number_format($this->price, 2);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'termly' => 'Termly',
            default => ucfirst($this->type),
        };
    }
}
