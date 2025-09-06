<?php

namespace App\Providers;

use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Define morph map aliases, but do NOT enforce, so existing records that store full class names still work
        Relation::morphMap([
            'staff' => Staff::class,
            'patient' => Patient::class,
            'message' => Message::class,
            'group_message' => GroupMessage::class,
        ]);
    }
}
