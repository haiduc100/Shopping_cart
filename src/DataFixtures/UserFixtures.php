<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $hasher;
    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($this->hasher->hashPassword($admin,'123'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $staff = new User();
        $staff->setUsername('staff');
        $staff->setPassword($this->hasher->hashPassword($staff,'123'));
        $staff->setRoles(['ROLE_STAFF']);
        $manager->persist($staff);

        $customer = new User();
        $customer->setUsername('customer');
        $customer->setPassword($this->hasher->hashPassword($customer,'123'));
        $customer->setRoles(['ROLE_CUSTOMER']);
        $manager->persist($customer);
        
        $manager->flush();
    }
}
