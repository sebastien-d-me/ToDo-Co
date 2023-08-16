<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testUserResponse(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail("admin@test.com");
        $client->loginUser($user);

        $crawler = $client->request("GET", "/users");

        $this->assertResponseIsSuccessful();
    }

    public function testUserCreate(): void 
    {
        $client = static::createClient();
        $client->followRedirects();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail("admin@test.com");
        $client->loginUser($user);

        $crawler = $client->request("GET", "/users/create");

        $form = $crawler->selectButton("Sauvegarder")->form();
        $form["user[username]"] = "john.doe";
        $client->submit($form);
        
        $this->assertSelectorTextContains("div.alert.alert-success", "L'utilisateur a bien été ajouté.");
    }
}
