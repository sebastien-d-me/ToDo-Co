<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route("/users", name: "users_list")]
    #[IsGranted("ROLE_ADMIN", message: "Vous n'avez pas les droits pour accéder à cette page.")]
    public function list(UserRepository $usersRepository): Response
    {
        $usersList = $usersRepository->findAll();

        return $this->render("pages/users/list.html.twig", [
            "users" => $usersList
        ]);
    }


    #[Route("/users/create", name: "users_create")]
    public function new(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get("role")->getData();
            $password = $userPasswordHasher->hashPassword($user, $form->get("password")->getData());
            $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

            $user->setRoles([$role]);
            $user->setPassword($password);
            $user->setCreatedAt($currentDate);
            $user->setUpdatedAt($currentDate);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute("home");
        }

        return $this->render("pages/users/create.html.twig", [
            "form" => $form,
        ]);
    }


    #[Route("/users/{userID}/edit", name: "users_edit")]
    #[IsGranted("ROLE_ADMIN", message: "Vous n'avez pas les droits pour accéder à cette page.")]
    public function edit(EntityManagerInterface $entityManager, Request $request, int $userID, UserPasswordHasherInterface $userPasswordHasher, UserRepository $usersRepository): Response
    {
        $user = $usersRepository->findOneBy(["id" => $userID]);
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get("role")->getData();
            $password = $userPasswordHasher->hashPassword($user, $form->get("password")->getData());
            $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

            $user->setRoles([$role]);
            $user->setPassword($password);
            $user->setUpdatedAt($currentDate);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "L'utilisateur a bien été modifié.");
            return $this->redirectToRoute("users_list");
        }

        return $this->render("pages/users/edit.html.twig", [
            "form" => $form,
            "user" => $user
        ]);
    }
}
