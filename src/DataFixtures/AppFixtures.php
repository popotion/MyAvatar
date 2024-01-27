<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@admin.fr');
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                'password'
            )
        );
        $user->setFirstName('Admin');
        $user->setLastName('ADMIN');
        $user->setIsVerified(true);
        $user->setRoles(['ROLE_SUPER_ADMIN']);

        $manager->persist($user);
        $manager->flush();
    }
}
