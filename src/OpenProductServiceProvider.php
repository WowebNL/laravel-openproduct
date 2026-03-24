<?php

namespace Woweb\Openproduct;

use Illuminate\Support\ServiceProvider;

class OpenProductServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/openproduct.php' => config_path('openproduct.php'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/openproduct.php',
            'openproduct'
        );
    }
}
