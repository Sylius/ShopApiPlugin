<?php

declare (strict_types = 1);

namespace Sylius\ShopApiPlugin\ViewRepository\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\CartViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Cart\CartSummaryView;
use Webmozart\Assert\Assert;
use Sylius\Component\Core\OrderCheckoutStates;

final class CartViewRepository implements CartViewRepositoryInterface
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var CartViewFactoryInterface */
    private $cartViewFactory;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        CartViewFactoryInterface $cartViewFactory
    ) {
        $this->cartRepository  = $cartRepository;
        $this->cartViewFactory = $cartViewFactory;
    }

    public function getOneByToken(string $orderToken): CartSummaryView
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->createCartQueryBuilder()
            ->andWhere('o.checkoutState != :checkoutState')
            ->andWhere('o.tokenValue = :tokenValue')
            ->setParameter('tokenValue', $orderToken)
            ->setParameter('checkoutState', OrderCheckoutStates::STATE_COMPLETED)
            ->getQuery()
            ->getOneOrNullResult();

        Assert::notNull($cart, 'Cart with given id does not exists');

        return $this->cartViewFactory->create($cart, $cart->getLocaleCode());
    }
}
