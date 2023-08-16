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
    
    public function testUserRepository()
    {
        $usersContainer = static::getContainer()->get(UserRepository::class);

        $user = new User();
        $user->setEmail("john.doe.".date("Y-m-d H:i:s")."@mail.com");
        $user->setUsername("john.doe.".date("Y-m-d H:i:s"));
        $user->setCreatedAt(\DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $user->setUpdatedAt(\DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $newHashedPassword = password_hash("Azerty123", PASSWORD_DEFAULT);
        $user->setPassword($newHashedPassword);

        $usersContainer->upgradePassword($user, $newHashedPassword);

        $this->assertEquals($newHashedPassword, $user->getPassword());
    }
}