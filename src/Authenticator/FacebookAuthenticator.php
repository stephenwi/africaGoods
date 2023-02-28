<?php

namespace App\Authenticator;

use App\Entity\User;
use App\Exception\EmailAlreadyUsedException;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class FacebookAuthenticator extends AbstractSocialAuthenticator
{

    protected string $serviceName = 'facebook';

    public function getUserFromResourceOwner(ResourceOwnerInterface $facebookUser, UserRepository $repository): ?User
    {
        if (!($facebookUser instanceof FacebookUser)) {
            throw new \RuntimeException('Expecting FacebookClient as the first parameter');
        }
        $user = $repository->findForOauth('facebook', $facebookUser->getId(), $facebookUser->getEmail());
        if ($user && null === $user->getFacebookId()) {
            throw new EmailAlreadyUsedException();
        }

        return $user;
    }
}