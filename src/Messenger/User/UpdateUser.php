<?php

namespace App\Messenger\User;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateUser
{
    public function __construct(
        public string $currentEmail,
        public User $user,
        public ?UploadedFile $profilePictureFile,
        public string $profilePictureDirectory,
    ) {
    }
}
