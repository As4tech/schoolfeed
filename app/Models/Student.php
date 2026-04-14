<?php

namespace App\Models;

use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[ScopedBy([SchoolScope::class])]
class Student extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->student_id)) {
                $student->student_id = $student->generateStudentId();
            }
        });
    }

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

    /**
     * Generate a unique student ID
     * 
     * @return string
     */
    public function generateStudentId(): string
    {
        // Get school prefix or use default
        $schoolPrefix = $this->school_id ? 'STU' : 'STU';
        
        // Get current year
        $year = date('Y');
        
        // Generate a random 4-digit number
        do {
            $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $studentId = "{$year}/{$schoolPrefix}/{$randomNumber}";
        } while (self::where('student_id', $studentId)->exists());
        
        return $studentId;
    }
}
