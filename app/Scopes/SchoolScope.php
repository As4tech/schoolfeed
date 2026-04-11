<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class SchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Don't apply scope to User model (needed for authentication)
        if ($model instanceof \App\Models\User) {
            return;
        }

        // Super Admins can see all data
        if (Auth::check() && Auth::user()->hasRole('Super Admin')) {
            return;
        }

        // For other users, filter by their school_id
        if (Auth::check() && Auth::user()->school_id) {
            $builder->where('school_id', Auth::user()->school_id);
        }
    }
}
