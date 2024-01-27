<?php

namespace App\Messenger\User\Handler;

use App\Messenger\User\UpdateUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateUserHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Filesystem $filesystem,
    ) {
    }
    public function __invoke(UpdateUser $command): void
    {
        $currentEmail = $command->currentEmail;
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
        } else if ($user->getEmail() !== $currentEmail) {
            $newProfilePicture = new UploadedFile($command->profilePictureDirectory . '/' . $user->getPictureProfileName(), $user->getPictureProfileName(), null, null, true);
            dump($newProfilePicture);
            $newFilename = md5($user->getEmail()) . '.' . $newProfilePicture->guessExtension();
            $currentFilename = md5($currentEmail) . '.' . $newProfilePicture->guessExtension();
            dump($currentFilename);

            try {
                $newProfilePicture->move(
                    $command->profilePictureDirectory,
                    $newFilename
                );
                $this->filesystem->remove($command->profilePictureDirectory . '/' . $currentFilename);
            } catch (FileException $e) {
            }

            $user->setPictureProfileName($newFilename);
        }

        $this->entityManager->persist($user);
    }
}
