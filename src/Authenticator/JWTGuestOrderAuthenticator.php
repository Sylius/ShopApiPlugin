<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Authenticator;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

final class JWTGuestOrderAuthenticator implements AuthenticatorInterface
{
    private const TOKEN_HEADER = 'Sylius-Guest-Token';

    public function supports(Request $request): bool
    {
        return $request->headers->has(self::TOKEN_HEADER);
    }

    public function getCredentials(Request $request): string
    {
        return $request->headers->get(self::TOKEN_HEADER);
    }

    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        try {
            return $userProvider->loadUserByUsername($credentials);
        } catch (JWTDecodeFailureException $decodeFailureException) {
            throw new AuthenticationException($decodeFailureException->getMessage());
        }
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        // The request continues
        return null;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse(['message' => 'Authentication required'], Response::HTTP_UNAUTHORIZED);
    }

    public function createAuthenticatedToken(UserInterface $user, $providerKey): GuardTokenInterface
    {
        return new PostAuthenticationGuardToken($user, $providerKey, []);
    }
}
