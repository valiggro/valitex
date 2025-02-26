<?php

namespace App\Security;

use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\Google as GoogleProvider;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\{RememberMeBadge, UserBadge};
use Symfony\Component\Security\Http\Authenticator\Passport\{Passport, SelfValidatingPassport};
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class GoogleOauth2Authenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private $resourceOwner;

    public function __construct(
        private GoogleProvider $googleProvider,
        private UserRepository $userRepository,
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->get('_route') == 'app_googleoauth2_callback';
    }

    public function authenticate(Request $request): Passport
    {
        $accessToken = $this->googleProvider->getAccessToken('authorization_code', [
            'code' => $request->query->get('code'),
        ]);
        $this->resourceOwner = $this->googleProvider->getResourceOwner(
            $accessToken
        );
        return new SelfValidatingPassport(
            userBadge: new UserBadge(
                userIdentifier: $this->resourceOwner->getEmail(),
                userLoader: [$this->userRepository, 'findOneByEmail'],
            ),
            badges: [
                new RememberMeBadge,
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse('/');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->getFlashBag()->add(
            'error',
            "Contul {$this->resourceOwner->getEmail()} nu are access. Contactează administratorul."
        );
        return new RedirectResponse('/');
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new RedirectResponse('/');
    }
}
