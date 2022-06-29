<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Event\CartPickedUp;
use Sylius\ShopApiPlugin\Handler\Cart\PickupCartHandler;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class PickupCartHandlerSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        MessageBusInterface $eventBus,
    ): void {
        $this->beConstructedWith(
            $cartFactory,
            $cartRepository,
            $channelRepository,
            $eventBus,
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(PickupCartHandler::class);
    }

    function it_handles_cart_pickup_for_not_logged_in_user(
        ChannelInterface $channel,
        CurrencyInterface $currency,
        ChannelRepositoryInterface $channelRepository,
        FactoryInterface $cartFactory,
        LocaleInterface $locale,
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        MessageBusInterface $eventBus,
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

        $cartRepository->add($cart)->shouldBeCalledOnce();

        $cartPickedUp = new CartPickedUp('ORDERTOKEN');

        $eventBus->dispatch($cartPickedUp, [new DispatchAfterCurrentBusStamp()])->willReturn(new Envelope($cartPickedUp))->shouldBeCalled();

        $this(new PickupCart('ORDERTOKEN', 'CHANNEL_CODE'));
    }

    function it_throws_an_exception_if_channel_is_not_found(
        ChannelRepositoryInterface $channelRepository,
    ): void {
        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PickupCart('ORDERTOKEN', 'CHANNEL_CODE'),
        ]);
    }
}
