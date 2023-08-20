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
        $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

        $user = new User();    
        $password = $this->userPasswordHasher->hashPassword($user, "Azerty123");
        $user->setEmail("john.doe@test.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($password);
        $user->setUsername("john.doe");
        $user->setCreatedAt($currentDate);
        $user->setUpdatedAt($currentDate);

        $this->assertEquals($user->getId(), $user->getId());
        $this->assertEquals("john.doe@test.com", $user->getEmail());
        $this->assertEquals("john.doe@test.com", $user->getUserIdentifier());
        $this->assertEquals(["ROLE_USER"], $user->getRoles());
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals("john.doe", $user->getUsername());
        $this->assertEquals($currentDate, $user->getCreatedAt());
        $this->assertEquals($currentDate, $user->getUpdatedAt());
        $this->assertNull($user->eraseCredentials());        
    }
}
