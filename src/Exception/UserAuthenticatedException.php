<?php

namespace App\Exception;

use App\Entity\User;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UserAuthenticatedException extends AuthenticationException
{

    public function __construct(private readonly User $user, private readonly ResourceOwnerInterface $resourceOwner)
    { }

    public function getUser(): User
    {
       return $this->user;
    }

    public function getResourceOwner(): ResourceOwnerInterface
    {
        return $this->resourceOwner;
    }
}