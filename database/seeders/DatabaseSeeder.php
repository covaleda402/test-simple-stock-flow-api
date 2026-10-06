<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Application\Ports\Outbound\UserRepositoryInterface;
use App\Application\Ports\Outbound\PasswordHasherInterface;
use App\Domain\Entity\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $userRepository = app(UserRepositoryInterface::class);
        $passwordHasher = app(PasswordHasherInterface::class);

        $adminEmail = env('ADMIN_EMAIL', 'admin@stockflow.local');
        $adminPassword = env('ADMIN_PASSWORD', 'admin123456');

        $normalized = User::normalizeUsername($adminEmail);

        if (!$userRepository->existsByUsername($normalized)) {
            $user = new User(
                '00000000-0000-4000-8000-000000000001',
                $normalized,
                $passwordHasher->hash($adminPassword),
                User::ROLE_ADMIN
            );
            $userRepository->save($user);
        }
    }
}
