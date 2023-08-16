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
        $form["user[username]"] = "john.doe";
        $form["user[password][first]"] = "Azerty123";
        $client->submit($form);

        $userCreated = $usersRepository->findOneBy(["username" => "john.doe"]);
        $this->assertNotNull($userCreated);
        
        $client->followRedirects();
        $this->assertSelectorTextContains("div.alert.alert-success", "L'utilisateur a bien été ajouté.");
    }
}
