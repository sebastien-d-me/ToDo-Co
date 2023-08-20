<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{
    #[Route("/tasks", name: "tasks_list_uncompleted")]
    #[IsGranted("ROLE_USER", message: "Vous n'avez pas les droits pour accéder à cette page.")]
    public function listUncompleted(TaskRepository $taskRepository): Response
    {
        $tasksList = $taskRepository->findByUncompleted();

        return $this->render("pages/tasks/list_uncompleted.html.twig", [
            "tasks" => $tasksList
        ]);
    }


    #[Route("/tasks/done", name: "tasks_list_completed")]
    #[IsGranted("ROLE_USER", message: "Vous n'avez pas les droits pour accéder à cette page.")]
    public function listCompleted(TaskRepository $taskRepository): Response
    {
        $tasksList = $taskRepository->findByCompleted();
        
        return $this->render("pages/tasks/list_completed.html.twig", [
            "tasks" => $tasksList
        ]);
    }


    #[Route("/tasks/create", name: "tasks_create")]
    #[IsGranted("ROLE_USER", message: "Vous n'avez pas les droits pour accéder à cette page.")]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $loggedUser = $this->getUser();
            $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

            $task->setUser($loggedUser);
            $task->setIsDone(false);
            $task->setCreatedAt($currentDate);
            $task->setUpdatedAt($currentDate);

            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash("success", "La tâche a été bien été ajoutée.");

            return $this->redirectToRoute("tasks_list_uncompleted");
        }

        return $this->render("pages/tasks/create.html.twig", [
            "form" => $form,
        ]);
    }


    #[Route("/tasks/edit/{taskId}/{type}", name: "tasks_edit")]
    #[IsGranted("ROLE_USER", message: "Vous n'avez pas les droits pour accéder à cette page.")]
    public function edit(EntityManagerInterface $entityManager, Request $request, int $taskId, TaskRepository $taskRepository, string $type): Response
    {
        $task = $taskRepository->findOneBy(["id" => $taskId]);
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

            $task->setUpdatedAt($currentDate);

            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash("success", "La tâche a été bien été modifié.");

            if($type === "completed") {
                return $this->redirectToRoute("tasks_list_completed");
            } else {
                return $this->redirectToRoute("tasks_list_uncompleted");
            }
        }

        return $this->render("pages/tasks/edit.html.twig", [
            "form" => $form,
        ]);
    }


    #[Route("/tasks/{taskId}/completed", name: "tasks_completed")]
    #[IsGranted("ROLE_USER", message: "Vous n'avez pas les droits pour accéder à cette page.")]
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
    #[IsGranted("ROLE_USER", message: "Vous n'avez pas les droits pour accéder à cette page.")]
    public function uncompleted(EntityManagerInterface $entityManager, int $taskId, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->findOneBy(["id" => $taskId]);
        $task->setIsDone(false);

        $entityManager->persist($task);
        $entityManager->flush();
        
        $this->addFlash("success", "La tâche ".$task->getTitle()." a bien été marquée comme non faite.");
        
        return $this->redirectToRoute("tasks_list_completed");
    }


    #[Route("/tasks/{taskId}/delete/{type}", name: "tasks_delete")]
    #[IsGranted("ROLE_USER", message: "Vous n'avez pas les droits pour accéder à cette page.")]
    public function delete(EntityManagerInterface $entityManager, int $taskId, TaskRepository $taskRepository, string $type)
    {
        $task = $taskRepository->findOneBy(["id" => $taskId]);

        if($this->isGranted("ROLE_ADMIN") || $this->getUser() === $task->getUser()) {
            $entityManager->remove($task);
            $entityManager->flush();

            $this->addFlash("success", "La tâche a bien été supprimée.");

            if($type === "completed") {
                return $this->redirectToRoute("tasks_list_completed");
            } else {
                return $this->redirectToRoute("tasks_list_uncompleted");
            }
        } else {
            $this->addFlash("danger", "Vous n'avez pas les droits pour supprimer cette tâche.");
            return $this->redirectToRoute("home");      
        }
    }
}
