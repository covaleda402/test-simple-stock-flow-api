<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Application\Ports\Outbound\OrderRepositoryInterface;
use App\Infrastructure\Persistence\Repository\ProductRepository;
use App\Infrastructure\Persistence\Repository\OrderRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Le decimos a Laravel qué repositorio usar cuando pidamos la interfaz
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    public function boot(): void
    {
        //
    }
}