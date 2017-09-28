<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\DropCart;
use Webmozart\Assert\Assert;

final class DropCartHandler
{
    /**
     * @var OrderRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param OrderRepositoryInterface $cartRepository
     */
    public function __construct(OrderRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function handle(DropCart $dropCart)
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $dropCart->orderToken()]);

        Assert::notNull($cart, sprintf('Order with %s token has not been found.', $dropCart->orderToken()));
        Assert::same(OrderInterface::STATE_CART, $cart->getState());

        $this->cartRepository->remove($cart);
    }
}
