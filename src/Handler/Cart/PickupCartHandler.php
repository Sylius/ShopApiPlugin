<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use Sylius\Bundle\CoreBundle\EventListener\UserCartRecalculationListener;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Webmozart\Assert\Assert;

final class PickupCartHandler
{
    /** @var FactoryInterface */
    private $cartFactory;

    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInShopUserProvider;

    public function __construct(
        FactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider
    ) {
        $this->cartFactory = $cartFactory;
        $this->cartRepository = $cartRepository;
        $this->channelRepository = $channelRepository;
        $this->loggedInShopUserProvider = $loggedInShopUserProvider;
    }

    /** @param PickupCart $pickupCart */
    public function handle(PickupCart $pickupCart)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($pickupCart->channelCode());

        Assert::notNull($channel, sprintf('Channel with %s code has not been found.', $pickupCart->channelCode()));

        if ($this->loggedInShopUserProvider->isUserLoggedIn()) {
            $this->handleUserCart($pickupCart->orderToken(), $channel);

            return;
        }

        /** @var OrderInterface $cart */
        $cart = $this->cartFactory->createNew();
        $cart->setChannel($channel);
        $cart->setCurrencyCode($channel->getBaseCurrency()->getCode());
        $cart->setLocaleCode($channel->getDefaultLocale()->getCode());
        $cart->setTokenValue($pickupCart->orderToken());

        $this->cartRepository->add($cart);
    }

    /**
     * If the user is logged in we do not need to create a cart.
     *
     * The reason: The `UserCartRecalculationListener` is called for every authenticated request. In the case the cart does not exist it creates one.
     *
     * @see UserCartRecalculationListener
     */
    private function handleUserCart(string $tokenValue, ChannelInterface $channel): void
    {
        $shopUser = $this->loggedInShopUserProvider->provide();
        $customer = $shopUser->getCustomer();

        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['customer' => $customer, 'channel' => $channel]);
        $cart->setTokenValue($tokenValue);
    }
}
