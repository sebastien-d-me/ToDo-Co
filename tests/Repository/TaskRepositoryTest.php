<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    private \Doctrine\ORM\EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get("doctrine")->getManager();
    }
    
    public function testFindByUncompleted(): void
    {
        $task = $this->entityManager->getRepository(Task::class)->findByUncompleted()[0];

        $this->assertSame(false, $task->IsIsDone());
    }

    public function testFindByCompleted(): void
    {
        $task = $this->entityManager->getRepository(Task::class)->findByCompleted()[0];

        $this->assertSame(true, $task->IsIsDone());
    }
}