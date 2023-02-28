<?php

namespace App\Authenticator;

use App\Entity\User;
use App\Exception\NotVerifiedEmailException;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class GoogleAuthenticator extends AbstractSocialAuthenticator
{
    protected string $serviceName = 'google';

    public function getUserFromResourceOwner(ResourceOwnerInterface $googleUser, UserRepository $repository): ?User
    {
        if (!($googleUser instanceof GoogleUser)) {
            throw new \RuntimeException('Expecting GoogleUser as the first parameter');
        }
        if (true !== ($googleUser->toArray()['email_verified'] ?? null)) {
            throw new NotVerifiedEmailException();
        }
        $user = $repository->findForOauth('google', $googleUser->getId(), $googleUser->getEmail());
        if ($user && null === $user->getGoogleId()) {
            $user->setGoogleId($googleUser->getId());
            $this->manager->flush();
        }

        return $user;
    }
}