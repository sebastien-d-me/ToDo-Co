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
        $loggedAccount = $usersRepository->findOneByEmail("admin@test.com");

        $client->loginUser($loggedAccount);

        $crawler = $client->request("GET", "/users");

        $this->assertResponseIsSuccessful();
    }

    public function testNewUser(): void 
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("admin@test.com");

        $client->loginUser($loggedAccount);

        $crawler = $client->request("POST", "/users/create");

        $form = $crawler->selectButton("Sauvegarder")->form();
        $form["user[username]"] = "john.doe.usercontroller";
        $form["user[password][first]"] = "Azerty123";
        $form["user[password][second]"] = "Azerty123";
        $form["user[email]"] = "john.doe.usercontroller@mail.com";
        $form["user[role]"] = "ROLE_ADMIN";

        $client->submit($form);
        $client->followRedirects();

        $user = $usersRepository->findOneBy(["email" => "john.doe.usercontroller@mail.com"]);

        $this->assertNotNull($user);
    }

    public function testEditUser(): void 
    {
        $client = static::createClient();

        $usersRepository = static::getContainer()->get(UserRepository::class);
        $loggedAccount = $usersRepository->findOneByEmail("admin@test.com");

        $client->loginUser($loggedAccount);

        $userId = $usersRepository->findOneByEmail("john.doe.usercontroller@mail.com")->getId();

        $crawler = $client->request("POST", "/users/".$userId."/edit");

        $form = $crawler->selectButton("Sauvegarder")->form();
        $form["user[username]"] = "john.doe.edited.usercontroller";
        $form["user[password][first]"] = "Azerty123";
        $form["user[password][second]"] = "Azerty123";
        $form["user[email]"] = "john.doe.usercontroller@mail.com";
        $form["user[role]"] = "ROLE_ADMIN";

        $client->submit($form);
        $client->followRedirects();

        $user = $usersRepository->findOneBy(["email" => "john.doe.usercontroller@mail.com"]);

        $this->assertEquals("john.doe.edited.usercontroller", $user->getUsername());
    }
}
