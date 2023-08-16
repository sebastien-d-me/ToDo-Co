<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testTaskUncompletedResponse(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail("admin@test.com");
        $client->loginUser($user);

        $crawler = $client->request("GET", "/tasks");

        $this->assertResponseIsSuccessful();
    }

    public function testTaskCompletedResponse(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail("admin@test.com");
        $client->loginUser($user);

        $crawler = $client->request("GET", "/tasks/done");

        $this->assertResponseIsSuccessful();
    }
}
