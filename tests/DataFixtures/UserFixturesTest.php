<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixturesTest extends KernelTestCase
{
    private UserPasswordHasherInterface $userPasswordHasher;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->userPasswordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testUserFixtures()
    {
        $objectManager = static::getContainer()->get("doctrine")->getManager();
        
        $usersContainer = static::getContainer()->get(UserRepository::class);
        
        $oldUsersNumber = count($usersContainer->findAll());

        $userFixtures = new UserFixtures($this->userPasswordHasher);
        $userFixtures->load($objectManager);

        $newUsersNumber = count($usersContainer->findAll());

        $this->assertEquals($newUsersNumber, $oldUsersNumber + 25);
    }
}