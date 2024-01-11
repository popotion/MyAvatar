<?php

namespace App\Messenger\User\Handler;

use App\Messenger\User\CreateUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
class CreateUserHandler
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManager,
    ) {

    }
    public function __invoke(CreateUser $command): void
    {
        $user = $command->user;
        $plainPassword = $command->plainPassword;

        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $plainPassword,
            )
        );
        $user->setIsVerified(true);

        $this->entityManager->persist($user);
    }
}
