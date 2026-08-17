<?php

namespace App\Domain\Command;

use App\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:create-manager-user',
    description: 'Creates a manager user with default credentials',
)]
class CreateManagerUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = 'admin3@mediabridge.admin.local';
        $username = 'admin3';

        $existingUser = $this->em->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);

        if ($existingUser) {
            $output->writeln('<error>User already exists</error>');
            return Command::FAILURE;
        }

        $user = new User($username, $email, 'root');

        $user->setUuid(Uuid::v4());
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'root');
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('<info>Manager user created successfully!</info>');

        return Command::SUCCESS;
    }
}