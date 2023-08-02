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
    #[Route("/tasks", name: "tasks_list")]
    public function list(TaskRepository $taskRepository): Response
    {
        $tasksList = $taskRepository->findAll();

        return $this->render("pages/tasks/list.html.twig", [
            "tasks" => $tasksList
        ]);
    }
}
