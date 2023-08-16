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

    public function testTaskFixturesDependencies()
    {
        $taskFixtures = new TaskFixtures();

        $this->assertContains(UserFixtures::class, $taskFixtures->getDependencies());
    }

    public function testTaskFixtures()
    {
        $objectManager = static::getContainer()->get("doctrine")->getManager();
        
        $tasksContainer = static::getContainer()->get(TaskRepository::class);
        $oldTasksNumber = count($tasksContainer->findAll());

        $taskFixtures = new TaskFixtures();
        $taskFixtures->load($objectManager);

        $newTasksNumber = count($tasksContainer->findAll());

        $this->assertEquals($newTasksNumber, $oldTasksNumber + 5);
    }
}