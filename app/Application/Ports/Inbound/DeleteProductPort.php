<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

interface DeleteProductPort
{
    public function execute(string $id): void;
}
