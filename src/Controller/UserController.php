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

class UserController extends AbstractController
{
    #[Route("/users", name: "users_list")]
    public function list(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");

        $userRoles = $this->getUser()->getRoles();
        if(!in_array("ROLE_ADMIN", $userRoles)) {
            return $this->redirectToRoute("home");
        }
        
        $usersList = $userRepository->findAll();

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
    public function edit(EntityManagerInterface $entityManager, Request $request, int $userID, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        $user = $userRepository->findBy(["id" => $userID]);
        $form = $this->createForm(UserType::class, $user);

        return $this->render("pages/users/edit.html.twig", [
            "form" => $form,
            "user" => $user
        ]);
    }
}
