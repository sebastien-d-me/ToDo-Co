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

    public function testUserFixtures(): void
    {
        $objectManager = static::getContainer()->get("doctrine")->getManager();
        
        $usersRepository = static::getContainer()->get(UserRepository::class);
        
        $oldUsersNumber = count($usersRepository->findAll());

        $userFixtures = new UserFixtures($this->userPasswordHasher, $usersRepository);
        $userFixtures->load($objectManager);

        $newUsersNumber = count($usersRepository->findAll());

        $this->assertEquals($newUsersNumber, $oldUsersNumber + 5);
    }
}