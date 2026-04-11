<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class School extends Model
{
    protected $fillable = [
        'name',
        'email',
        'paystack_subaccount_code',
        'phone',
        'address',
        'is_active',
        'subscription_ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_ends_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return $this->is_active && ($this->subscription_ends_at === null || $this->subscription_ends_at->isFuture());
    }

    protected static function booted(): void
    {
        static::creating(function (School $school) {
            if (empty($school->slug) && !empty($school->name)) {
                $base = Str::slug($school->name);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $school->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
