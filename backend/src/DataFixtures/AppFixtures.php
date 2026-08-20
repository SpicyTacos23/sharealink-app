<?php

namespace App\DataFixtures;

use App\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Demo users for local development and the public demo instance.
 *
 * ⚠️ These credentials are intentionally public (documented in the project
 * README) and must NEVER be reused if this app is ever deployed with real
 * user data. They exist purely so reviewers/recruiters can try the app
 * without registering an account.
 */
class AppFixtures extends Fixture
{
    private const DEMO_USERS = [
        [
            'email'    => 'admin.demo@local',
            'username' => 'AdminDemo',
            'roles'    => ['ROLE_ADMIN'],
            'password' => 'adminDemo123',
        ],
        [
            'email'    => 'user1.demo@local',
            'username' => 'User1_demo',
            'roles'    => ['ROLE_USER'],
            'password' => 'userDemo123',
        ],
        [
            'email'    => 'user2.demo@local',
            'username' => 'User2_demo',
            'roles'    => ['ROLE_USER'],
            'password' => 'userDemo456',
        ],
    ];

    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        foreach (self::DEMO_USERS as $data) {
            // The constructor requires a plain-text password; it is immediately
            // overwritten below with the hashed value via setPassword().
            $user = new User($data['username'], $data['email'], $data['password']);
            $user->setUuid(Uuid::v4()->toRfc4122());
            $user->setRoles($data['roles']);
            $user->setPassword($this->hasher->hashPassword($user, $data['password']));

            $manager->persist($user);
        }

        $manager->flush();
    }
}