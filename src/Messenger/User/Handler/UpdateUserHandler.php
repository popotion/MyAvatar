<?php

namespace App\Messenger\User\Handler;

use App\Messenger\User\UpdateUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateUserHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }
    public function __invoke(UpdateUser $command): void
    {
        $user = $command->user;
        $profilePictureFile = $command->profilePictureFile;

        if ($profilePictureFile) {
            $newFilename = md5($user->getEmail()) . '.' . $profilePictureFile->guessExtension();

            try {
                $profilePictureFile->move(
                    $command->profilePictureDirectory,
                    $newFilename
                );
            } catch (FileException $e) {
            }

            $user->setPictureProfileName($newFilename);
        }

        $this->entityManager->persist($user);
    }
}
