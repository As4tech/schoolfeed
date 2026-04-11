<?php

use App\Models\School;

if (! function_exists('currentSchool')) {
    function currentSchool(): ?School
    {
        return app()->bound('school') ? app('school') : null;
    }
}
