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

    public function testTaskCreate(): void 
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $loggedUser = $usersRepository->findOneByEmail("user@test.com");
        $client->loginUser($loggedUser);

        $crawler = $client->request("POST", "/tasks/create");

        $form = $crawler->selectButton("Sauvegarder")->form();
        $form["task[title]"] = "Test Controller : Nunc gravida ligula non est convallis faucibus";
        $form["task[content]"] = "Morbi sit amet molestie sem. Quisque mattis posuere neque ullamcorper pulvinar.";

        $client->submit($form);
        $client->followRedirects();

        $task = $tasksRepository->findOneBy(["title" => "Test Controller : Nunc gravida ligula non est convallis faucibus"]);

        $this->assertNotNull($task);
    }

    public function testTaskEditUncompleted(): void 
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedUser = $usersRepository->findOneByEmail("user@test.com");
        $client->loginUser($loggedUser);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $tasksList = $tasksRepository->findBy([
            "isDone" => false
        ]);
        $exempleTask = $tasksList[0];
        $exempleTaskStatut = "uncompleted";

        $crawler = $client->request("POST", "/tasks/edit/".$exempleTask->getId()."/".$exempleTaskStatut);

        $form = $crawler->selectButton("Sauvegarder")->form();
        $form["task[title]"] = "Test Controller Edited : Nunc gravida ligula non est convallis faucibus";
        $form["task[content]"] = "Edited : Morbi sit amet molestie sem. Quisque mattis posuere neque ullamcorper pulvinar.";

        $client->submit($form);
        $client->followRedirects();

        $task = $tasksRepository->findOneBy(["title" => "Test Controller Edited : Nunc gravida ligula non est convallis faucibus"]);

        $this->assertNotNull($task);
    }

    public function testTaskEditCompleted(): void 
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedUser = $usersRepository->findOneByEmail("user@test.com");
        $client->loginUser($loggedUser);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $tasksList = $tasksRepository->findBy([
            "isDone" => true
        ]);
        $exempleTask = $tasksList[0];
        $exempleTaskStatut = "completed";

        $crawler = $client->request("POST", "/tasks/edit/".$exempleTask->getId()."/".$exempleTaskStatut);

        $form = $crawler->selectButton("Sauvegarder")->form();
        $form["task[title]"] = "Test Controller Edited : Nunc gravida ligula non est convallis faucibus";
        $form["task[content]"] = "Edited : Morbi sit amet molestie sem. Quisque mattis posuere neque ullamcorper pulvinar.";

        $client->submit($form);
        $client->followRedirects();

        $task = $tasksRepository->findOneBy(["title" => "Test Controller Edited : Nunc gravida ligula non est convallis faucibus"]);

        $this->assertNotNull($task);
    }

    public function testTaskSetCompleted(): void
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $user = $usersRepository->findOneByEmail("user@test.com");
        $client->loginUser($user);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $tasksList = $tasksRepository->findAll();
        $exempleTask = $tasksList[0];

        $crawler = $client->request("GET", "/tasks/".$exempleTask->getId()."/completed");
        $client->followRedirects();

        $this->assertEquals(true, $exempleTask->isIsDone());
    }

    public function testTaskSetUncompleted(): void
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $user = $usersRepository->findOneByEmail("user@test.com");
        $client->loginUser($user);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $tasksList = $tasksRepository->findAll();
        $exempleTask = $tasksList[0];

        $crawler = $client->request("GET", "/tasks/".$exempleTask->getId()."/uncompleted");
        $client->followRedirects();

        $this->assertEquals(false, $exempleTask->isIsDone());
    }

    public function testTaskDeleteUncompleted(): void
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $user = $usersRepository->findOneByEmail("admin@test.com");
        $client->loginUser($user);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $tasksList = $tasksRepository->findBy([
            "isDone" => false
        ]);
        $exempleTask = $tasksList[0];
        $exempleTaskStatut = "uncompleted";

        $crawler = $client->request("DELETE", "/tasks/".$exempleTask->getId()."/delete/".$exempleTaskStatut);
        $client->followRedirects();

        $checkExempleTask = $tasksList[0];

        $this->assertEquals($exempleTask->getTitle(), $checkExempleTask->getTitle());
    }

    public function testTaskDeleteUser(): void
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $user = $usersRepository->findOneByEmail("user@test.com");
        $client->loginUser($user);

        $admin = $usersRepository->findOneByEmail("admin@test.com");
        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $tasksList = $tasksRepository->findBy([
            "user" => $admin
        ]);
        $exempleTask = $tasksList[0];
        $exempleTask->isIsDone() ? $exempleTaskStatut = "completed" : $exempleTaskStatut = "uncompleted";

        $crawler = $client->request("DELETE", "/tasks/".$exempleTask->getId()."/delete/".$exempleTaskStatut);
        $client->followRedirects();

        $checkExempleTask = $tasksList[0];

        $this->assertEquals($exempleTask->getTitle(), $checkExempleTask->getTitle());

        if($exempleTaskStatut === "completed") {
            $this->assertEquals("completed", $exempleTaskStatut);
        } else {
            $this->assertEquals("uncompleted", $exempleTaskStatut);
        }

        if($exempleTaskStatut === "completed") {
            $tasksRepository = static::getContainer()->get(TaskRepository::class);
            $exempleTask = $tasksList[0];
            $crawler = $client->request("DELETE", "/tasks/".$exempleTask->getId()."/delete/uncompleted");
            $client->followRedirects();

            $checkExempleTask = $tasksList[0];
            
            $this->assertEquals($exempleTask->getTitle(), $checkExempleTask->getTitle());
        }
        
    }
    

    public function testTaskDeleteCompleted(): void
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $user = $usersRepository->findOneByEmail("admin@test.com");
        $client->loginUser($user);

        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $tasksList = $tasksRepository->findBy([
            "isDone" => true
        ]);
        $exempleTask = $tasksList[0];
        $exempleTaskStatut = "completed";

        $crawler = $client->request("DELETE", "/tasks/".$exempleTask->getId()."/delete/".$exempleTaskStatut);
        $client->followRedirects();

        $checkExempleTask = $tasksList[0];

        $this->assertEquals($exempleTask->getTitle(), $checkExempleTask->getTitle());
    }
}
