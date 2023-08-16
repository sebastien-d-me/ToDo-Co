<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEntityTest extends WebTestCase
{
    private UserPasswordHasherInterface $userPasswordHasher;

    protected function setUp(): void
    {
        $this->userPasswordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testCreateUser(): void
    {        
        $email = "john.doe@test.com";
        $role = ["ROLE_USER"];
        $username = "john.doe";
        $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

        $user = new User();    
        $password = $this->userPasswordHasher->hashPassword($user, "Azerty123");
        $user->setEmail($email);
        $user->setRoles($role);
        $user->setPassword($password);
        $user->setUsername($username);
        $user->setCreatedAt($currentDate);
        $user->setUpdatedAt($currentDate);

        $this->assertEquals($user->getId(), $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($email, $user->getUserIdentifier());
        $this->assertEquals($role, $user->getRoles());
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($currentDate, $user->getCreatedAt());
        $this->assertEquals($currentDate, $user->getUpdatedAt());
        $this->assertNull($user->eraseCredentials());        
    }
}
