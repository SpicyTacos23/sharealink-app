<?php

namespace App\DataFixtures;

use App\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email'    => 'admin@dev.local',
                'username' => 'Admin',
                'roles'    => ['ROLE_ADMIN'],
                'password' => 'admin123',
            ],
            [
                'email'    => 'user1@dev.local',
                'username' => 'TestUser1',
                'roles'    => ['ROLE_USER'],
                'password' => 'user123',
            ],
            [
                'email'    => 'user2@dev.local',
                'username' => 'TestUser2',
                'roles'    => ['ROLE_USER'],
                'password' => 'user123',
            ],
        ];

        foreach ($users as $data) {
            $user = new User($data['username'], $data['email'], 'root');
            $user->setUuid(uniqid('user_', true));
            $user->setRoles($data['roles']);
            $user->setPassword($this->hasher->hashPassword($user, $data['password']));
        
            $manager->persist($user);
        }

        $manager->flush();
    }
}
