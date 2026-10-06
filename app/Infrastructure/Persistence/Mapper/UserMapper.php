<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mapper;

use App\Domain\Entity\User;
use App\Infrastructure\Persistence\Model\UserModel;

final class UserMapper
{
    public static function toDomain(UserModel $model): User
    {
        return new User(
            $model->id,
            $model->username,
            $model->password_hash,
            $model->role
        );
    }

    public static function toPersistence(User $domain): array
    {
        return [
            'id' => $domain->id(),
            'username' => $domain->username(),
            'password_hash' => $domain->passwordHash(),
            'role' => $domain->role(),
        ];
    }
}
