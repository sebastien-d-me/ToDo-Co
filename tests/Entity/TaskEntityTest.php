<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskEntityTest extends WebTestCase
{
    public function testCreateTask(): void
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $usersList = $usersRepository->findAll();

        $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
        $currentUser = $usersList[array_rand($usersList)];

        $task = new Task();
        $task->setTitle("Lorem ipsum dolor");
        $task->setContent("Donec lobortis sapien id sapien tincidunt feugiat. Sed urna ante, egestas vitae luctus sit amet, semper in risus.");
        $task->setIsDone(true);
        $task->setCreatedAt($currentDate);
        $task->setUpdatedAt($currentDate);
        $task->setUser($currentUser);

        $this->assertEquals($task->getId(), $task->getId());
        $this->assertEquals("Lorem ipsum dolor", $task->getTitle());
        $this->assertEquals("Donec lobortis sapien id sapien tincidunt feugiat. Sed urna ante, egestas vitae luctus sit amet, semper in risus.", $task->getContent());
        $this->assertEquals(true, $task->isIsDone());
        $this->assertEquals($currentDate, $task->getCreatedAt());
        $this->assertEquals($currentDate, $task->getUpdatedAt());
        $this->assertEquals($currentUser, $task->getUser());
    }
}
