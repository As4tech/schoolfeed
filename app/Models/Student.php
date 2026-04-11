<?php

namespace App\Models;

use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ScopedBy([SchoolScope::class])]
class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'first_name',
        'last_name',
        'student_id',
        'class_id',
        'grade',
        'parent_id',
        'status',
        'date_of_birth',
        'gender',
        'allergies',
        'medical_notes',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'parent_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'parent_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function feedingPlans(): BelongsToMany
    {
        return $this->belongsToMany(FeedingPlan::class, 'student_plan')
            ->withPivot(['start_date', 'end_date', 'status', 'amount_paid', 'notes'])
            ->withTimestamps();
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(FeedingAttendance::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
