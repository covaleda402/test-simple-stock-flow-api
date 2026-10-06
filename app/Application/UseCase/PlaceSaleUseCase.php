<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\PlaceSalePort;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Application\Ports\Outbound\SaleRepositoryInterface;
use App\Application\Ports\Outbound\TransactionManagerInterface;
use App\Domain\Entity\Sale;
use App\Domain\Exception\BusinessRuleValidationException;
use App\Domain\ValueObject\Quantity;
use DateTimeImmutable;
use DateTimeZone;

final class PlaceSaleUseCase implements PlaceSalePort
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private ProductRepositoryInterface $productRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private TransactionManagerInterface $transactionManager
    ) {}

    public function execute(array $lines, string $soldByUsername, string $soldByUserId): string
    {
        // 1. lines vacío
        if (empty($lines)) {
            throw new BusinessRuleValidationException("La venta debe tener al menos un ítem.");
        }

        // 2. productos repetidos
        $seen = [];
        foreach ($lines as $line) {
            $productId = $line['productId'] ?? '';
            if (isset($seen[$productId])) {
                throw new BusinessRuleValidationException("La venta tiene productos repetidos.");
            }
            $seen[$productId] = true;
        }

        // Ejecutar de forma atómica dentro de la transacción
        return $this->transactionManager->transactional(function () use ($lines, $soldByUsername, $soldByUserId) {
            $saleId = self::generateUuidV4();
            $soldAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
            $sale = new Sale($saleId, $soldByUsername, $soldByUserId, $soldAt);

            foreach ($lines as $line) {
                $productId = $line['productId'] ?? '';
                $rawQuantity = $line['quantity'] ?? 0;

                // 3. El producto no existe o está dado de baja
                $product = $this->productRepository->findActiveById($productId);
                if ($product === null) {
                    throw new BusinessRuleValidationException("El producto {$productId} no existe.");
                }

                // 4. quantity menor o igual a cero
                if (!is_int($rawQuantity) || $rawQuantity <= 0) {
                    throw new BusinessRuleValidationException("La cantidad debe ser mayor a cero.");
                }

                $quantity = new Quantity($rawQuantity);

                // 5. stock insuficiente (se valida dentro de Sale::addItem -> Product::withdraw)
                $category = $this->categoryRepository->findById($product->categoryId());
                $categoryName = $category !== null ? $category->name() : 'General';

                $itemId = self::generateUuidV4();
                $sale->addItem($itemId, $product, $categoryName, $quantity);

                // Guardar stock actualizado del producto
                $this->productRepository->save($product);
            }

            $sale->ensureConfirmable();
            $this->saleRepository->save($sale);

            return $sale->id();
        });
    }

    private static function generateUuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
