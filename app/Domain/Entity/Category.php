<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\BusinessRuleValidationException;

final class Category
{
    private string $id;
    private string $name;

    public function __construct(string $id, string $name)
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            throw new BusinessRuleValidationException("El nombre de la categoría no puede estar vacío.");
        }

        $this->id = $id;
        $this->name = $trimmed;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
