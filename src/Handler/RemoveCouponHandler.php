<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Command\RemoveCoupon;
use Webmozart\Assert\Assert;

final class RemoveCouponHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderProcessor = $orderProcessor;
    }

    public function handle(RemoveCoupon $removeCoupon): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $removeCoupon->orderToken()]);

        Assert::notNull($cart, sprintf('Cart with token %s has not been found.', $removeCoupon->orderToken()));

        $cart->setPromotionCoupon(null);

        $this->orderProcessor->process($cart);
    }
}
