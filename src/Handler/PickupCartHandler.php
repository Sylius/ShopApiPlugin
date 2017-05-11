<?php

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Webmozart\Assert\Assert;

final class PickupCartHandler
{
    /**
     * @var FactoryInterface
     */
    private $cartFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param FactoryInterface $cartFactory
     * @param OrderRepositoryInterface $cartRepository
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(FactoryInterface $cartFactory, OrderRepositoryInterface $cartRepository, ChannelRepositoryInterface $channelRepository)
    {
        $this->cartFactory = $cartFactory;
        $this->cartRepository = $cartRepository;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @param PickupCart $pickupCart
     */
    public function handle(PickupCart $pickupCart)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($pickupCart->channelCode());

        Assert::notNull($channel, sprintf('Channel with %s code has not been found.', $pickupCart->channelCode()));

        /** @var OrderInterface $cart */
        $cart = $this->cartFactory->createNew();
        $cart->setChannel($channel);
        $cart->setCurrencyCode($channel->getBaseCurrency()->getCode());
        $cart->setLocaleCode($channel->getDefaultLocale()->getCode());
        $cart->setTokenValue($pickupCart->token());

        $this->cartRepository->add($cart);
    }
}
