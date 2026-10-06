<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\ListCategoriesPort;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;

final class ListCategoriesUseCase implements ListCategoriesPort
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(): array
    {
        $categories = $this->categoryRepository->findAll();

        return array_map(static fn ($cat) => [
            'id' => $cat->id(),
            'name' => $cat->name(),
        ], $categories);
    }
}
