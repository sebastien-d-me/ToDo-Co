<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomeResponse(): void
    {
        $client = static::createClient();
        $crawler = $client->request("GET", "/");

        $this->assertResponseIsSuccessful();
    }
}
