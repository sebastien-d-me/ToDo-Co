<?php

namespace App\Security;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface {
    public function __construct(private UrlGeneratorInterface $urlGenerator) {}

    public function checkPreAuth(UserInterface $user): void {
        if($user->getUsername() === "anonymous") {
            $message = "Vous n'avez pas accès à ce compte.";
            throw new CustomUserMessageAccountStatusException($message);
        }
    }

    public function checkPostAuth(UserInterface $user): void {}
}