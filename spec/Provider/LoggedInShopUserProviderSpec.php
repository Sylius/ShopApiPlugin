<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class LoggedInShopUserProviderSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage): void
    {
        $this->beConstructedWith($tokenStorage);
    }

    function it_is_reviewer_subject_provider(): void
    {
        $this->shouldImplement(LoggedInShopUserProviderInterface::class);
    }

    function it_throws_an_error_if_there_is_no_shop_user_logged_in(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        UserInterface $anotherUser
    ): void {
        $tokenStorage->getToken()->willReturn(null, $token);
        $token->getUser()->willReturn(null, $anotherUser);

        $this->shouldThrow(TokenNotFoundException::class)->during('provide');
        $this->shouldThrow(TokenNotFoundException::class)->during('provide');
        $this->shouldThrow(TokenNotFoundException::class)->during('provide');
    }

    function it_returns_the_logged_in_user_if_there_is_one(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        ShopUserInterface $shopUser
    ): void {
        $token->getUser()->willReturn($shopUser);
        $tokenStorage->getToken()->willReturn($token);

        $this->provide()->shouldReturn($shopUser);
    }

    function it_checks_if_shop_user_is_logged_in(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        UserInterface $anotherUser
    ): void {
        $tokenStorage->getToken()->willReturn(null, $token);
        $token->getUser()->willReturn(null, $anotherUser, $shopUser);

        $this->check()->shouldReturn(false);
        $this->check()->shouldReturn(false);
        $this->check()->shouldReturn(false);
        $this->check()->shouldReturn(true);
    }
}
