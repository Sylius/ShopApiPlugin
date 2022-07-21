<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\CartViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Cart\CartSummaryView;
use Webmozart\Assert\Assert;

final class CartViewRepository implements CartViewRepositoryInterface
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var CartViewFactoryInterface */
    private $cartViewFactory;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        CartViewFactoryInterface $cartViewFactory,
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartViewFactory = $cartViewFactory;
    }

    public function getOneByToken(string $token): CartSummaryView
    {
        /** @var OrderInterface|null $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $token, 'state' => OrderInterface::STATE_CART]);
        Assert::notNull($cart, 'Cart with given id does not exists');

        return $this->cartViewFactory->create($cart, $cart->getLocaleCode());
    }
}
