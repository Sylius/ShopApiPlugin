<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\EventListener\Messenger;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Sylius\ShopApiPlugin\Event\CartPickedUp;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CartPickedUpListenerSpec extends ObjectBehavior
{
    function let(LoggedInShopUserProviderInterface $loggedInShopUserProvider, MessageBusInterface $bus): void
    {
        $this->beConstructedWith($loggedInShopUserProvider, $bus);
    }

    function it_assigns_customer_to_cart_when_user_is_logged_in(
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
        MessageBusInterface $bus,
        ShopUserInterface $shopUser,
        CustomerInterface $customer
    ): void {
        $loggedInShopUserProvider->isUserLoggedIn()->willReturn(true);

        $loggedInShopUserProvider->provide()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('peter@parker.com');

        $assignCustomerToCart = new AssignCustomerToCart('ORDERTOKEN', 'peter@parker.com');

        $bus->dispatch($assignCustomerToCart)->willReturn(new Envelope($assignCustomerToCart))->shouldBeCalled();

        $this(new CartPickedUp('ORDERTOKEN'));
    }

    function it_does_nothing_if_user_is_not_logged_in(
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
        MessageBusInterface $bus
    ): void {
        $loggedInShopUserProvider->isUserLoggedIn()->willReturn(false);

        $bus->dispatch(Argument::any())->shouldNotBeCalled();

        $this(new CartPickedUp('ORDERTOKEN'));
    }
}
