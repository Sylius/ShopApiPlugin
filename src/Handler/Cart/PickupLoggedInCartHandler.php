<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\PickupLoggedInCart;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Webmozart\Assert\Assert;

final class PickupLoggedInCartHandler
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInShopUserProvider;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider
    ) {
        $this->cartRepository = $cartRepository;
        $this->channelRepository = $channelRepository;
        $this->loggedInShopUserProvider = $loggedInShopUserProvider;
    }

    public function handle(PickupLoggedInCart $pickupLoggedInCart)
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($pickupLoggedInCart->channelCode());
        Assert::notNull($channel, sprintf('Channel with %s code has not been found.', $pickupLoggedInCart->channelCode()));

        $shopUser = $this->loggedInShopUserProvider->provide();
        $customer = $shopUser->getCustomer();

        /** @var OrderInterface|null $cart */
        $cart = $this->cartRepository->findOneBy(['customer' => $customer, 'channel' => $channel]);
        Assert::notNull($cart, 'The cart was not created by the event listener');

        $cart->setTokenValue($pickupLoggedInCart->orderToken());
    }
}
