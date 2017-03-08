<?php

namespace Elkbullwinkle\XeroLaravel;

use Illuminate\Support\ServiceProvider;

class XeroLaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/xero-laravel.php' => config_path('xero-laravel.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('XeroLaravel', function ($app) {
            return new XeroLaravel();
        });
    }
}
