<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
        //Observers
        \App\Models\Announcement::observe(\App\Observers\AnnouncementObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Student::observe(\App\Observers\StudentObserver::class);
        \App\Models\StudentClass::observe(\App\Observers\StudentClassObserver::class);
        \App\Models\Year::observe(\App\Observers\YearObserver::class);

        Inertia::share([
            'auth' => fn () => [
                'user' => Auth::user(),
            ],
        ]);
    }
}
