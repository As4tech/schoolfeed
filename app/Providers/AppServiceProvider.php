<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $helpers = app_path('Support/helpers.php');
        if (File::exists($helpers)) {
            require_once $helpers;
        }

        // Share school branding data with guest layout
        View::composer('layouts.guest', function ($view) {
            $school = null;
            $schoolName = null;
            $schoolLogo = null;

            // Try to get school from route
            $route = Route::current();
            if ($route && $route->hasParameter('school')) {
                $schoolSlug = $route->parameter('school');
                $school = \App\Models\School::where('slug', $schoolSlug)->first();
                
                if ($school) {
                    $schoolName = \App\Helpers\SettingsHelper::getSetting($school->id, 'school_name', $school->name);
                    $schoolLogo = \App\Helpers\SettingsHelper::getSetting($school->id, 'logo', null);
                }
            }

            $view->with([
                'school' => $school,
                'schoolName' => $schoolName,
                'schoolLogo' => $schoolLogo
            ]);
        });
    }
}
