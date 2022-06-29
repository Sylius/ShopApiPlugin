<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\DropCart;
use Webmozart\Assert\Assert;

final class DropCartHandler
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    public function __construct(OrderRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function __invoke(DropCart $dropCart): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $dropCart->orderToken()]);

        Assert::notNull($cart, sprintf('Order with %s token has not been found.', $dropCart->orderToken()));
        Assert::same(OrderInterface::STATE_CART, $cart->getState());

        $this->cartRepository->remove($cart);
    }
}
