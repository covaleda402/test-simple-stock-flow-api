<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

interface RegisterSellerPort
{
    /**
     * @param string $username
     * @param string $password
     * @param string $role
     * @return string user id
     */
    public function execute(string $username, string $password, string $role): string;
}
