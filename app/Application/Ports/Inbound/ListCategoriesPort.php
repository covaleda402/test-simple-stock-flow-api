<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

interface ListCategoriesPort
{
    /**
     * @return array<int, array{id: string, name: string}>
     */
    public function execute(): array;
}
