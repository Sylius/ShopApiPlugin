<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Event\CartPickedUp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

final class PickupCartHandler
{
    /** @var FactoryInterface */
    private $cartFactory;

    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var MessageBusInterface */
    private $eventBus;

    public function __construct(
        FactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        MessageBusInterface $eventBus
    ) {
        $this->cartFactory = $cartFactory;
        $this->cartRepository = $cartRepository;
        $this->channelRepository = $channelRepository;
        $this->eventBus = $eventBus;
    }

    public function __invoke(PickupCart $pickupCart): void
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($pickupCart->channelCode());

        Assert::notNull($channel, sprintf('Channel with %s code has not been found.', $pickupCart->channelCode()));

        /** @var OrderInterface $cart */
        $cart = $this->cartFactory->createNew();
        $cart->setChannel($channel);
        $cart->setCurrencyCode($channel->getBaseCurrency()->getCode());
        $cart->setLocaleCode($pickupCart->getLocaleCode() ?? $channel->getDefaultLocale()->getCode());
        $cart->setTokenValue($pickupCart->orderToken());

        $this->cartRepository->add($cart);

        $this->eventBus->dispatch(new CartPickedUp($pickupCart->orderToken()), [new DispatchAfterCurrentBusStamp()]);
    }
}
