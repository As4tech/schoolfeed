<?php

namespace App\Models;

use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([SchoolScope::class])]
class WeeklyMealSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'meal_id',
        'day_of_week',
        'price',
        'week_start_date',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'week_start_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }

    public function mealSelections(): HasMany
    {
        return $this->hasMany(MealSelection::class);
    }

    public function getDayNameAttribute(): string
    {
        return ucfirst($this->day_of_week);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'GHS ' . number_format($this->price, 2);
    }

    public function scopeForWeek($query, $weekStartDate)
    {
        return $query->where('week_start_date', $weekStartDate);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }
}
