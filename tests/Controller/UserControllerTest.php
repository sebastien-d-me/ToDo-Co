<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testUserResponse(): void
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $user = $usersRepository->findOneByEmail("admin@test.com");
        $client->loginUser($user);

        $crawler = $client->request("GET", "/users");

        $this->assertResponseIsSuccessful();
    }

    public function testUserCreate(): void 
    {
        $client = static::createClient();
        $usersRepository = static::getContainer()->get(UserRepository::class);
        $user = $usersRepository->findOneByEmail("admin@test.com");
        $client->loginUser($user);

        $crawler = $client->request("GET", "/users/create");

        $form = $crawler->selectButton("Sauvegarder")->form();
        $form["user[username]"] = "john.doe.usercontroller";
        $form["user[password][first]"] = "Azerty123";
        $form["user[password][second]"] = "Azerty123";
        $form["user[email]"] = "john.doe.usercontroller@mail.com";
        $form["user[role]"] = "ROLE_ADMIN";

        $client->submit($form);
        $client->followRedirects();

        $createdUser = $usersRepository->findOneByEmail("john.doe.usercontroller@mail.com");

        $this->assertEquals(["ROLE_ADMIN"], $createdUser->getRoles()[0]);
        $this->assertSelectorTextContains("span.alert-message", "L'utilisateur a bien été ajouté.");
    }
}
