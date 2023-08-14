<?php

namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskEntityTest extends WebTestCase
{
    public function testCreateTask(): void
    {
        $client = static::createClient();
        $usersContainer = static::getContainer()->get(UserRepository::class);
        $usersList = $usersContainer->findAll();

        $title = "Lorem ipsum dolor";
        $content = "Donec lobortis sapien id sapien tincidunt feugiat. Sed urna ante, egestas vitae luctus sit amet, semper in risus.";
        $isDone = true;
        $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
        $randomUser = $usersList[array_rand($usersList)];

        $task = new Task();
        $task->setTitle($title);
        $task->setContent($content);
        $task->setIsDone($isDone);
        $task->setCreatedAt($currentDate);
        $task->setUpdatedAt($currentDate);
        $task->setUser($randomUser);

        $this->assertEquals($task->getId(), $task->getId());
        $this->assertEquals($title, $task->getTitle());
        $this->assertEquals($content, $task->getContent());
        $this->assertEquals($isDone, $task->isIsDone());
        $this->assertEquals($currentDate, $task->getCreatedAt());
        $this->assertEquals($currentDate, $task->getUpdatedAt());
        $this->assertEquals($randomUser, $task->getUser());
    }
}
