<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\PickupLoggedInCart;
use Sylius\ShopApiPlugin\Handler\Cart\PickupLoggedInCartHandler;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;

final class PickupLoggedInCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider
    ): void {
        $this->beConstructedWith(
            $cartRepository,
            $channelRepository,
            $loggedInShopUserProvider
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(PickupLoggedInCartHandler::class);
    }

    function it_handles_cart_pickup_for_a_logged_in_user(
        ChannelInterface $channel,
        ChannelRepositoryInterface $channelRepository,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
        ShopUserInterface $user,
        CustomerInterface $customer,
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository
    ): void {
        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel);

        $loggedInShopUserProvider->provide()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $cartRepository->findOneBy(['customer' => $customer, 'channel' => $channel])->willReturn($cart);
        $cart->setTokenValue('ORDERTOKEN')->shouldBeCalled();

        $this->handle(new PickupLoggedInCart('ORDERTOKEN', 'CHANNEL_CODE'));
    }

    function it_throws_an_exception_if_channel_is_not_found(
        ChannelRepositoryInterface $channelRepository
    ): void {
        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [new PickupLoggedInCart('ORDERTOKEN', 'CHANNEL_CODE')]);
    }

    function it_throws_an_exception_when_no_order_was_found(
        ChannelInterface $channel,
        ChannelRepositoryInterface $channelRepository,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
        ShopUserInterface $user,
        CustomerInterface $customer,
        OrderRepositoryInterface $cartRepository
    ): void {
        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel);

        $loggedInShopUserProvider->provide()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $cartRepository->findOneBy(['customer' => $customer, 'channel' => $channel])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [new PickupLoggedInCart('ORDERTOKEN', 'CHANNEL_CODE')]);
    }
}
