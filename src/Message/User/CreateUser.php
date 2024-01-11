<?php

namespace App\Message\User;

use App\Entity\User;

class CreateUser
{
    public function __construct(
        public User $user,
    ) {
    }
}
