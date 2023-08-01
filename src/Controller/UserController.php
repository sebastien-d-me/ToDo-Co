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
        $users = $userRepository->findAll();

        return $this->render("pages/users/list.html.twig", [
            "users" => $users
        ]);
    }


    #[Route("/users/create", name: "users_create")]
    public function new(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordHasher->hashPassword($user, $form->get("password")->getData());
            $currentDate = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

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
}
