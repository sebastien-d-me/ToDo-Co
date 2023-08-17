<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testTaskUncompletedResponse(): void
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $user = $usersRepository->findOneByEmail("admin@test.com");
        $client->loginUser($user);

        $crawler = $client->request("GET", "/tasks");

        $this->assertResponseIsSuccessful();
    }

    public function testTaskCompletedResponse(): void
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $user = $usersRepository->findOneByEmail("admin@test.com");
        $client->loginUser($user);

        $crawler = $client->request("GET", "/tasks/done");

        $this->assertResponseIsSuccessful();
    }

    public function testTaskDelete(): void
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $user = $usersRepository->findOneByEmail("admin@test.com");
        $client->loginUser($user);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $tasksList = $tasksRepository->findAll();
        $exempleTask = $tasksList[0];
        $exempleTask->isIsDone() ? $exempleTaskStatut = "completed" : $exempleTaskStatut = "uncompleted";

        $crawler = $client->request("DELETE", "/tasks/".$exempleTask->getId()."/delete/".$exempleTaskStatut);
        $client->followRedirects();

        $checkExempleTask = $tasksList[0];

        $this->assertEquals($exempleTask->getTitle(), $checkExempleTask->getTitle());
    }
}
