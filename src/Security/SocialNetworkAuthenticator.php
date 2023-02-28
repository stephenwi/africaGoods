<?php

namespace App\Security;

use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GithubClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SocialNetworkAuthenticator extends SocialAuthenticator
{
    use TargetPathTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(RouterInterface $router, ClientRegistry $clientRegistry, UserRepository $repository)
    {
        $this->router = $router;
        $this->clientRegistry = $clientRegistry;
        $this->repository = $repository;
    }

    public function start(Request $request, AuthenticationException $exception = null) {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function supports(Request $request) {
        return 'oauth_check' === $request->attributes->get('_route') && $request->get('service') === 'github';
    }

    public function getCredentials(Request $request) {
          return $this->fetchAccessToken($this->getClient());
    }

    /**
     * @param AccessToken $credentials
     */
    public function getUser($credentials, UserProviderInterface $userProvider) {
        /**
         * @var GithubResourceOwner $githubUser
         */
        $githubUser = $this->getClient()->fetchUserFromToken($credentials);
        return $this->repository->findOrCreateOauth($githubUser);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey) {

       if($targetPath = $this->getTargetPath($request->getSession(), $providerKey)){
           return new RedirectResponse($targetPath ?: '/');
       }
        return new RedirectResponse('/');
    }

    private function getClient(): GithubClient
    {
        return $this->clientRegistry->getClient('github');
    }
}