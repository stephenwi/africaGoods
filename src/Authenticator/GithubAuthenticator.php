<?php

namespace App\Authenticator;

use App\Entity\User;
use App\Exception\NotVerifiedEmailException;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpClient\HttpClient;

class GithubAuthenticator extends AbstractSocialAuthenticator
{

    protected string $serviceName = 'github';

    public function getUserFromResourceOwner(ResourceOwnerInterface $githubUser, UserRepository $repository): ?User
    {
        if (!($githubUser instanceof GithubResourceOwner)) {
            throw new \RuntimeException('Expecting GithubResourceOwner as the first parameter');
        }
        $user = $repository->findForOauth('github', $githubUser->getId(), $githubUser->getEmail());
        if ($user && null === $user->getGithubId()) {
            $user->setGithubId($githubUser->getId());
            $this->manager->flush();
        }

        return $user;
    }

    public function getResourceOwnerFromCredentials(AccessToken $credentials): GithubResourceOwner
    {
        /** @var GithubResourceOwner $githubUser */
        $githubUser = parent::getResourceOwnerFromCredentials($credentials);
        $response = HttpClient::create()->request(
            'GET',
            'https://api.github.com/user/emails',
            [
                'headers' => [
                    'authorization' => "token {$credentials->getToken()}",
                ],
            ]
        );
        $emails = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        foreach ($emails as $email) {
            if (true === $email['primary'] && true === $email['verified']) {
                $data = $githubUser->toArray();
                $data['email'] = $email['email'];

                return new GithubResourceOwner($data);
            }
        }

        throw new NotVerifiedEmailException();
    }
}