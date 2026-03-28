<?php

namespace Sidu\DapodikFilament;

use Illuminate\Support\ServiceProvider;

class DapodikFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sidu-dapodik-filament');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/sidu-dapodik-filament'),
        ], 'sidu-dapodik-filament-views');
    }
}
