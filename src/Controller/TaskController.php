<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route("/tasks", name: "tasks_list_uncompleted")]
    public function listUncompleted(TaskRepository $taskRepository): Response
    {
        $tasksList = $taskRepository->findBy([
            "isDone" => false
        ]);

        return $this->render("pages/tasks/list_uncompleted.html.twig", [
            "tasks" => $tasksList
        ]);
    }


    #[Route("/tasks/done", name: "tasks_list_completed")]
    public function listCompleted(TaskRepository $taskRepository): Response
    {
        $tasksList = $taskRepository->findBy([
            "isDone" => true
        ]);

        return $this->render("pages/tasks/list_completed.html.twig", [
            "tasks" => $tasksList
        ]);
    }


    #[Route("/tasks/{taskId}/completed", name: "tasks_completed")]
    public function completed(EntityManagerInterface $entityManager, int $taskId, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->findOneBy(["id" => $taskId]);
        $task->setIsDone(true);

        $entityManager->persist($task);
        $entityManager->flush();
        
        $this->addFlash("success", "La tâche ".$task->getTitle()." a bien été marquée comme faite.");
        return $this->redirectToRoute("tasks_list_uncompleted");
    }


    #[Route("/tasks/{taskId}/uncompleted", name: "tasks_uncompleted")]
    public function uncompleted(EntityManagerInterface $entityManager, int $taskId, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->findOneBy(["id" => $taskId]);
        $task->setIsDone(false);

        $entityManager->persist($task);
        $entityManager->flush();
        
        $this->addFlash("success", "La tâche ".$task->getTitle()." a bien été marquée comme non faite.");
        return $this->redirectToRoute("tasks_list_completed");
    }
}
