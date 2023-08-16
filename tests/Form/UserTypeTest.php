<?php

namespace App\Form;

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
        $formData = [
            "username" => "john.doe",
            "password" => "Azerty123",
            "email" => "john.doe@mail.com",
        ];

        $user = new User();
        $form = $this->factory->create(UserType::class, $user);

        $expected = new User();
        $expected->setUsername($formData["username"]);
        $expected->setPassword($this->userPasswordHasher->hashPassword($expected, $formData["password"]));
        $expected->setEmail($formData["email"]);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $user);
    }
}