<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInUserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class LoggedInUserProviderSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage): void
    {
        $this->beConstructedWith($tokenStorage);
    }

    function it_is_reviewer_subject_provider(): void
    {
        $this->shouldImplement(LoggedInUserProviderInterface::class);
    }

    function it_throws_an_error_if_there_is_no_user_logged_in(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token
    ): void {
        $token->getUser()->shouldBeCalled()->willReturn(null);
        $tokenStorage->getToken()->shouldBeCalled()->willReturn($token);

        $this->shouldThrow(TokenNotFoundException::class)->during('provide');
    }

    function it_returns_the_logged_in_user_if_there_is_one(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        ShopUserInterface $shopUser
    ): void {
        $token->getUser()->shouldBeCalled()->willReturn($shopUser);
        $tokenStorage->getToken()->shouldBeCalled()->willReturn($token);

        $this->provide()->shouldReturn($shopUser);
    }
}
