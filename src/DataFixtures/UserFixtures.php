<?php

namespace App\DataFixtures;

use App\Entity\Partner;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    )
    {
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 2; $i++) {
            $user = new Partner();
            $user->setEmail("user$i@tt.paytop");

            $password = $this->hasher->hashPassword($user, "paytop$i");
            $user->setPassword($password);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
