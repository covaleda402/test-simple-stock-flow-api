<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

interface GetMediaPort
{
    public function execute(string $key): ?string;
}
