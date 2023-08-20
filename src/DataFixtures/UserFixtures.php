<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $userPasswordHasher, UserRepository $usersRepository)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->userRepository = $usersRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $checkExist = $this->userRepository->findOneBy([
            "email" => "user@test.com"
        ]);

        if($checkExist === null) {
            $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $faker->date("Y-m-d H:i:s"));
            
            $user = new User();
            $user->setEmail("user@test.com");
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, "Azerty123"));
            $user->setUsername("user");
            $user->setCreatedAt($currentDate);
            $user->setUpdatedAt($currentDate);

            $manager->persist($user);

            $user = new User();
            $user->setEmail("admin@test.com");
            $user->setRoles(["ROLE_ADMIN"]);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, "Azerty123"));
            $user->setUsername("admin");
            $user->setCreatedAt($currentDate);
            $user->setUpdatedAt($currentDate);

            $manager->persist($user);
        }

        for ($users = 0; $users < 5; $users++) { 
            $username = $faker->userName().".".$faker->word();
            $email = $username."@".$faker->freeEmailDomain();
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
