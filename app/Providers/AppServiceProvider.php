<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Los bindings de puertos e infraestructura viven exclusivamente en App\Bootstrap\PortBindingsServiceProvider (ADR-005)
    }

    public function boot(): void
    {
        //
    }
}