<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;

final class PickupCartHandlerSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider
    ): void {
        $this->beConstructedWith(
            $cartFactory,
            $cartRepository,
            $channelRepository,
            $loggedInShopUserProvider
        );
    }

    function it_handles_cart_pickup_for_not_logged_in_user(
        ChannelInterface $channel,
        CurrencyInterface  $currency,
        ChannelRepositoryInterface $channelRepository,
        FactoryInterface $cartFactory,
        LocaleInterface $locale,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository
    ): void {
        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel);
        $channel->getBaseCurrency()->willReturn($currency);
        $channel->getDefaultLocale()->willReturn($locale);
        $currency->getCode()->willReturn('EUR');
        $locale->getCode()->willReturn('de_DE');

        $cartFactory->createNew()->willReturn($cart);

        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setTokenValue('ORDERTOKEN')->shouldBeCalled();
        $cart->setCurrencyCode('EUR')->shouldBeCalled();
        $cart->setLocaleCode('de_DE')->shouldBeCalled();

        $loggedInShopUserProvider->isUserLoggedIn()->willReturn(false);
        $cart->setCustomer(Argument::any())->shouldNotBeCalled();

        $cartRepository->add($cart)->shouldBeCalledOnce();

        $this->handle(new PickupCart('ORDERTOKEN', 'CHANNEL_CODE'));
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

        $loggedInShopUserProvider->isUserLoggedIn()->willReturn(true);
        $loggedInShopUserProvider->provide()->willReturn($user);

        $user->getCustomer()->willReturn($customer);
        $cartRepository->findOneBy(['customer' => $customer, 'channel' => $channel])->willReturn($cart);
        $cart->setTokenValue('ORDERTOKEN')->shouldBeCalled();

        $cartRepository->add(Argument::any())->shouldNotBeCalled();

        $this->handle(new PickupCart('ORDERTOKEN', 'CHANNEL_CODE'));
    }

    function it_throws_an_exception_if_channel_is_not_found(
        ChannelRepositoryInterface $channelRepository
    ): void {
        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new PickupCart('ORDERTOKEN', 'CHANNEL_CODE'),
        ]);
    }
}
