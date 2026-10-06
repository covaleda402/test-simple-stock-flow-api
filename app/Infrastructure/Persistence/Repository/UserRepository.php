<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Application\Ports\Outbound\UserRepositoryInterface;
use App\Domain\Entity\User;
use App\Infrastructure\Persistence\Mapper\UserMapper;
use App\Infrastructure\Persistence\Model\UserModel;

final class UserRepository implements UserRepositoryInterface
{
    public function findById(string $id): ?User
    {
        $model = UserModel::where('id', $id)->first();
        if ($model === null) {
            return null;
        }

        return UserMapper::toDomain($model);
    }

    public function findByUsername(string $username): ?User
    {
        $model = UserModel::where('username', $username)->first();
        if ($model === null) {
            return null;
        }

        return UserMapper::toDomain($model);
    }

    public function save(User $user): void
    {
        $data = UserMapper::toPersistence($user);

        UserModel::updateOrCreate(
            ['id' => $user->id()],
            $data
        );
    }

    public function existsByUsername(string $username): bool
    {
        return UserModel::where('username', $username)->exists();
    }
}
