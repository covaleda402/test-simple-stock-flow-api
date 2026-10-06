<?php

declare(strict_types=1);

namespace App\Bootstrap;

use Illuminate\Support\ServiceProvider;

// Outbound Ports
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\SaleRepositoryInterface;
use App\Application\Ports\Outbound\UserRepositoryInterface;
use App\Application\Ports\Outbound\PasswordHasherInterface;
use App\Application\Ports\Outbound\TokenGeneratorInterface;
use App\Application\Ports\Outbound\FileStorageInterface;
use App\Application\Ports\Outbound\TransactionManagerInterface;

// Inbound Ports
use App\Application\Ports\Inbound\CreateProductPort;
use App\Application\Ports\Inbound\UpdateProductPort;
use App\Application\Ports\Inbound\DeleteProductPort;
use App\Application\Ports\Inbound\GetProductByIdPort;
use App\Application\Ports\Inbound\ListProductsPort;
use App\Application\Ports\Inbound\UploadProductImagePort;
use App\Application\Ports\Inbound\ListCategoriesPort;
use App\Application\Ports\Inbound\PlaceSalePort;
use App\Application\Ports\Inbound\GetSaleByIdPort;
use App\Application\Ports\Inbound\ListSalesPort;
use App\Application\Ports\Inbound\GetSalesReportPort;
use App\Application\Ports\Inbound\LoginPort;
use App\Application\Ports\Inbound\RegisterSellerPort;
use App\Application\Ports\Inbound\GetMediaPort;

// Infrastructure Implementations
use App\Infrastructure\Persistence\Repository\ProductRepository;
use App\Infrastructure\Persistence\Repository\CategoryRepository;
use App\Infrastructure\Persistence\Repository\SaleRepository;
use App\Infrastructure\Persistence\Repository\UserRepository;
use App\Infrastructure\Persistence\Transaction\DatabaseTransactionManager;
use App\Infrastructure\Security\Argon2PasswordHasher;
use App\Infrastructure\Security\JwtTokenService;
use App\Infrastructure\Storage\LocalFileStorage;

// Application Use Cases
use App\Application\UseCase\CreateProductUseCase;
use App\Application\UseCase\UpdateProductUseCase;
use App\Application\UseCase\DeleteProductUseCase;
use App\Application\UseCase\GetProductByIdUseCase;
use App\Application\UseCase\ListProductsUseCase;
use App\Application\UseCase\UploadProductImageUseCase;
use App\Application\UseCase\ListCategoriesUseCase;
use App\Application\UseCase\PlaceSaleUseCase;
use App\Application\UseCase\GetSaleByIdUseCase;
use App\Application\UseCase\ListSalesUseCase;
use App\Application\UseCase\GetSalesReportUseCase;
use App\Application\UseCase\LoginUseCase;
use App\Application\UseCase\RegisterSellerUseCase;
use App\Application\UseCase\GetMediaUseCase;

final class PortBindingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 1. Outbound Ports -> Infrastructure Adapters
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SaleRepositoryInterface::class, SaleRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PasswordHasherInterface::class, Argon2PasswordHasher::class);
        $this->app->bind(TokenGeneratorInterface::class, JwtTokenService::class);
        $this->app->bind(FileStorageInterface::class, LocalFileStorage::class);
        $this->app->bind(TransactionManagerInterface::class, DatabaseTransactionManager::class);

        // 2. Inbound Ports -> Application Use Cases
        $this->app->bind(CreateProductPort::class, CreateProductUseCase::class);
        $this->app->bind(UpdateProductPort::class, UpdateProductUseCase::class);
        $this->app->bind(DeleteProductPort::class, DeleteProductUseCase::class);
        $this->app->bind(GetProductByIdPort::class, GetProductByIdUseCase::class);
        $this->app->bind(ListProductsPort::class, ListProductsUseCase::class);
        $this->app->bind(UploadProductImagePort::class, UploadProductImageUseCase::class);
        $this->app->bind(ListCategoriesPort::class, ListCategoriesUseCase::class);
        $this->app->bind(PlaceSalePort::class, PlaceSaleUseCase::class);
        $this->app->bind(GetSaleByIdPort::class, GetSaleByIdUseCase::class);
        $this->app->bind(ListSalesPort::class, ListSalesUseCase::class);
        $this->app->bind(GetSalesReportPort::class, GetSalesReportUseCase::class);
        $this->app->bind(LoginPort::class, LoginUseCase::class);
        $this->app->bind(RegisterSellerPort::class, RegisterSellerUseCase::class);
        $this->app->bind(GetMediaPort::class, GetMediaUseCase::class);
    }

    public function boot(): void
    {
        //
    }
}
