<?php

namespace App\Infrastructure\Repository\Doctrine;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface, PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly ValidatorInterface $validator)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }


    public function updateUsername(string $uuid, string $newUsername): void
    {
        $user = $this->findOneBy(['uuid' => $uuid]);

        if (!$user) {
            throw new \InvalidArgumentException("User not found");
        }

        $user->setUsername($newUsername);

        // Validación con Symfony Validator
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function updateAvatar(string $uuid, string $newAvatar): void
    {
        $user = $this->findOneBy(['uuid' => $uuid]);

        if (!$user) {
            throw new \InvalidArgumentException("User not found");
        }

        $user->setAvatar($newAvatar);

        // Validación con Symfony Validator
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
