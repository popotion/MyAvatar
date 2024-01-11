<?php

namespace App\Messenger\User;

use App\Entity\User;

class CreateUser
{
    public function __construct(
        public User $user,
        public string $plainPassword,
    ) {
    }
}
