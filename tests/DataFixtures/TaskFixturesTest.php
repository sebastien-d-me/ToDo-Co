<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskFixturesTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testTaskFixturesDependencies(): void
    {
        $taskFixtures = new TaskFixtures();

        $this->assertContains(UserFixtures::class, $taskFixtures->getDependencies());
    }

    public function testTaskFixtures(): void
    {
        $objectManager = static::getContainer()->get("doctrine")->getManager();
        
        $tasksRepository = static::getContainer()->get(TaskRepository::class);
        $oldTasksNumber = count($tasksRepository->findAll());

        $taskFixtures = new TaskFixtures();
        $taskFixtures->load($objectManager);

        $newTasksNumber = count($tasksRepository->findAll());

        $this->assertEquals($newTasksNumber, $oldTasksNumber + 10);
    }
}