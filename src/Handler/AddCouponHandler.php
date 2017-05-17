<?php

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Sylius\ShopApiPlugin\Command\AddCoupon;
use Webmozart\Assert\Assert;

final class AddCouponHandler
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var PromotionCouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param PromotionCouponRepositoryInterface $couponRepository
     * @param OrderProcessorInterface $orderProcessor
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $couponRepository,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->orderRepository = $orderRepository;
        $this->couponRepository = $couponRepository;
        $this->orderProcessor = $orderProcessor;
    }

    /**
     * @param AddCoupon $addCoupon
     */
    public function handle(AddCoupon $addCoupon)
    {
        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $addCoupon->orderToken()]);

        Assert::notNull($cart, sprintf('Cart with token %s has not been found.', $addCoupon->orderToken()));

        $coupon = $this->couponRepository->findOneBy(['code' => $addCoupon->couponCode()]);

        Assert::notNull($coupon, sprintf('Coupon with code %s has not been found.', $addCoupon->couponCode()));

        $cart->setPromotionCoupon($coupon);

        $this->orderProcessor->process($cart);
    }
}
