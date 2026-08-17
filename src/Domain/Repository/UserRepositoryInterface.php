<?php

namespace App\Domain\Repository;

use App\Domain\Entity\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface UserRepositoryInterface
{
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void;
    public function find(int $id): mixed;
    public function findByEmail(string $email): ?User;
    public function updateUsername(string $uuid, string $newUsername): void;
    public function updateAvatar(string $uuid, string $newAvatar): void;
}
