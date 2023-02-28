<?php

namespace App\UserChecker;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
        if (!$user->setIsActive()){
            throw new CustomUserMessageAccountStatusException('Must confirm your account.');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
    }
}