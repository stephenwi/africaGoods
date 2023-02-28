<?php

namespace App\Exception;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UserOauthNotFoundException extends AuthenticationException
{

    public function __construct(private readonly ResourceOwnerInterface $resourceOwner)
    {

    }

    public function getResourceOwner(): ResourceOwnerInterface
    {
       return $this->resourceOwner;
    }
}