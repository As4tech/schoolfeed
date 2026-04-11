<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealSelection extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'weekly_meal_schedule_id',
        'parent_id',
        'meal_date',
        'price',
        'status',
        'payment_id',
        'notes',
    ];

    protected $casts = [
        'meal_date' => 'date',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function weeklyMealSchedule(): BelongsTo
    {
        return $this->belongsTo(WeeklyMealSchedule::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'parent_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'GHS ' . number_format($this->price, 2);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'selected' => 'Selected',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'selected' => 'blue',
            'paid' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForWeek($query, $weekStartDate)
    {
        $startDate = $weekStartDate;
        $endDate = $startDate->copy()->addDays(4); // Monday to Friday
        
        return $query->whereBetween('meal_date', [$startDate, $endDate]);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeSelected($query)
    {
        return $query->where('status', 'selected');
    }
}
