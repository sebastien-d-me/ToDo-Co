<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }
    
    public function testUserRepository(): void
    {
        $usersRepository = static::getContainer()->get(UserRepository::class);

        $user = new User();
        $user->setEmail("john.doe.repository@mail.com");
        $user->setUsername("john.doe.repository");
        $user->setCreatedAt(\DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $user->setUpdatedAt(\DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $newHashedPassword = password_hash("Azerty123", PASSWORD_DEFAULT);
        $user->setPassword($newHashedPassword);

        $usersRepository->upgradePassword($user, $newHashedPassword);

        $this->assertEquals($newHashedPassword, $user->getPassword());
    }
}