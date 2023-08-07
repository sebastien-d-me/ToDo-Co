<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($users = 0; $users < 25; $users++) { 
            $username = $faker->userName();
            $email = $username."@".$faker->freeEmailDomain();;
            $role = $faker->randomElements(["ROLE_USER", "ROLE_ADMIN"]);
            $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $faker->date("Y-m-d H:i:s"));

            $user = new User();    

            $password = $this->userPasswordHasher->hashPassword($user, $faker->password());

            $user->setEmail($email);
            $user->setRoles($role);
            $user->setPassword($password);
            $user->setUsername($username);
            $user->setCreatedAt($currentDate);
            $user->setUpdatedAt($currentDate);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
