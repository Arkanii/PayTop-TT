<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Customer;
use App\Entity\Partner;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    )
    {
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        $admin = new Admin();
        $admin->setEmail("admin@tt.paytop");

        $password = $this->hasher->hashPassword($admin, "paytop");
        $admin->setPassword($password);

        $manager->persist($admin);

        for ($i_partner = 1; $i_partner <= 2; $i_partner++) {
            $partner = new Partner();
            $partner->setEmail("user$i_partner@tt.paytop");

            $password = $this->hasher->hashPassword($partner, "paytop$i_partner");
            $partner->setPassword($password);

            $manager->persist($partner);

            for ($i_customer = 1; $i_customer <= 5; $i_customer++) {
                $customer = new Customer();
                $customer
                    ->setEmail("customer$i_customer@tt.paytop")
                    ->setPhone(trim(strrev(chunk_split(strrev(random_int(1000000000, 9999999999)),2, ' '))))
                    ->setLastName("lastname$i_customer")
                    ->setFirstName("firstname$i_customer")
                    ->setCreated()
                    ->setPartner($partner);

                $manager->persist($customer);
            }
        }

        $manager->flush();
    }
}
