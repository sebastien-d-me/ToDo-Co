<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $users = $manager->getRepository(User::class)->findAll();

        for ($tasks = 0; $tasks < 10; $tasks++) { 
            $title = rtrim($faker->sentence(3), ".");
            $content = $faker->paragraph();
            $isDone = $faker->boolean();
            $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $faker->date("Y-m-d H:i:s"));

            $task = new Task();
            if($tasks < 5) {
                $task->setUser($users[$tasks]);
            } else {
                $task->setUser(null);
            }
            $task->setTitle($title);
            $task->setContent($content);
            $task->setIsDone($isDone);
            $task->setCreatedAt($currentDate);
            $task->setUpdatedAt($currentDate);

            $manager->persist($task);
        }

        $manager->flush();
    }
}