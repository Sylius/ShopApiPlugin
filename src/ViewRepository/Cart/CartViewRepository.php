<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\CartViewFactoryInterface;
use Sylius\ShopApiPlugin\View\CartSummaryView;
use Webmozart\Assert\Assert;

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
        $this->cartRepository = $cartRepository;
        $this->cartViewFactory = $cartViewFactory;
    }

    public function getOneByToken(string $orderToken): CartSummaryView
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $orderToken]);
        Assert::notNull($cart, 'Cart with given id does not exists');

        return $this->cartViewFactory->create($cart, $cart->getLocaleCode());
    }
}
