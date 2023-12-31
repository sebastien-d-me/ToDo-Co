<?php

namespace App\Tests\Form;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTypeTest extends TypeTestCase
{
    private UserPasswordHasherInterface $userPasswordHasher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);
    }

    public function testUserType(): void
    {   
        $user = new User();

        $formData = [
            "username" => "john.doe.type",
            "password" => "Azerty123",
            "email" => "john.doe.type@mail.com",
        ];
        $form = $this->factory->create(UserType::class, $user);
        $form->submit($formData);

        $expected = new User();
        $expected->setUsername($formData["username"]);
        $expected->setPassword($this->userPasswordHasher->hashPassword($expected, $formData["password"]));
        $expected->setEmail($formData["email"]);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $user);
    }
}