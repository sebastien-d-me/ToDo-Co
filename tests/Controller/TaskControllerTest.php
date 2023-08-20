<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testUncompletedTasksResponse(): void
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("admin@test.com");

        $client->loginUser($loggedAccount);

        $crawler = $client->request("GET", "/tasks");

        $this->assertResponseIsSuccessful();
    }

    public function testCompletedTasksResponse(): void
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("admin@test.com");

        $client->loginUser($loggedAccount);

        $crawler = $client->request("GET", "/tasks/done");

        $this->assertResponseIsSuccessful();
    }

    public function testNewTask(): void 
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("user@test.com");
        
        $client->loginUser($loggedAccount);

        $crawler = $client->request("POST", "/tasks/create");

        $form = $crawler->selectButton("Sauvegarder")->form();
        $form["task[title]"] = "Task controller title";
        $form["task[content]"] = "Task controller content.";

        $client->submit($form);
        $client->followRedirects();

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $task = $tasksRepository->findOneBy(["title" => "Task controller title"]);

        $this->assertNotNull($task);
    }

    public function testEditUncompletedTask(): void 
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("user@test.com");
        
        $client->loginUser($loggedAccount);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $exempleTask = $tasksRepository->findOneBy(["isDone" => false]);

        $crawler = $client->request("POST", "/tasks/edit/".$exempleTask->getId()."/uncompleted");

        $form = $crawler->selectButton("Sauvegarder")->form();
        $form["task[title]"] = "Task edited controller title";
        $form["task[content]"] = "Task edited controller content.";

        $client->submit($form);
        $client->followRedirects();

        $task = $tasksRepository->findOneBy(["title" => "Task edited controller title"]);

        $this->assertNotNull($task);
    }

    public function testEditCompletedTask(): void 
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("user@test.com");

        $client->loginUser($loggedAccount);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $exempleTask = $tasksRepository->findOneBy(["isDone" => true]);

        $crawler = $client->request("POST", "/tasks/edit/".$exempleTask->getId()."/completed");

        $form = $crawler->selectButton("Sauvegarder")->form();
        $form["task[title]"] = "Task edited controller title";
        $form["task[content]"] = "Task edited controller content.";

        $client->submit($form);
        $client->followRedirects();

        $task = $tasksRepository->findOneBy(["title" => "Task edited controller title"]);

        $this->assertNotNull($task);
    }

    public function testSetCompletedTask(): void
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("user@test.com");
        
        $client->loginUser($loggedAccount);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $exempleTask = $tasksRepository->findOneBy(["isDone" => false]);

        $crawler = $client->request("GET", "/tasks/".$exempleTask->getId()."/completed");
        $client->followRedirects();

        $this->assertEquals(true, $exempleTask->isIsDone());
    }

    public function testSetUncompletedTask(): void
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("user@test.com");

        $client->loginUser($loggedAccount);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $exempleTask = $tasksRepository->findOneBy(["isDone" => true]);

        $crawler = $client->request("GET", "/tasks/".$exempleTask->getId()."/uncompleted");
        $client->followRedirects();

        $this->assertEquals(false, $exempleTask->isIsDone());
    }

    public function testDeleteUncompletedTask(): void
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("admin@test.com");
        
        $client->loginUser($loggedAccount);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $exempleTask = $tasksRepository->findOneBy(["isDone" => false]);

        $crawler = $client->request("DELETE", "/tasks/".$exempleTask->getId()."/delete/uncompleted");
        $client->followRedirects();

        $this->assertNull($exempleTask->getId());
    }

    public function testDeleteCompletedTask(): void
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("admin@test.com");
        
        $client->loginUser($loggedAccount);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $exempleTask = $tasksRepository->findOneBy(["isDone" => true]);

        $crawler = $client->request("DELETE", "/tasks/".$exempleTask->getId()."/delete/completed");
        $client->followRedirects();

        $this->assertNull($exempleTask->getId());
    }

    public function testDeleteTaskUser(): void
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("user@test.com");
        
        $client->loginUser($loggedAccount);

        $admin = $usersRepository->findOneByEmail("admin@test.com");
        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $exempleTask = $tasksRepository->findOneBy(["user" => $admin]);
        $exempleTask->isIsDone() ? $exempleTaskStatut = "completed" : $exempleTaskStatut = "uncompleted";

        $crawler = $client->request("DELETE", "/tasks/".$exempleTask->getId()."/delete/".$exempleTaskStatut);
        $client->followRedirects();

        $this->assertNotNull($exempleTask->getId());
    }
}
