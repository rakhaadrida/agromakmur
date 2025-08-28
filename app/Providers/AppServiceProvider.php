<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        \Carbon\Carbon::setLocale('id');

        View::composer('includes.navbar', function ($view) {
            $user = User::query()->findOrFail(Auth::user()->id);

            $view->with('notificationLists', $user->notifications()->whereNull('read_at')->limit(4)->get());
        });

        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
