<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
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
     * @var PromotionCouponEligibilityCheckerInterface
     */
    private $couponEligibilityChecker;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param PromotionCouponRepositoryInterface $couponRepository
     * @param OrderProcessorInterface $orderProcessor
     * @param PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $couponRepository,
        OrderProcessorInterface $orderProcessor,
        PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker
    ) {
        $this->orderRepository = $orderRepository;
        $this->couponRepository = $couponRepository;
        $this->orderProcessor = $orderProcessor;
        $this->couponEligibilityChecker = $couponEligibilityChecker;
    }

    /**
     * @param AddCoupon $addCoupon
     */
    public function handle(AddCoupon $addCoupon)
    {
        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $addCoupon->orderToken()]);

        Assert::notNull($cart, sprintf('Cart with token %s has not been found.', $addCoupon->orderToken()));

        /** @var PromotionCouponInterface $coupon */
        $coupon = $this->couponRepository->findOneBy(['code' => $addCoupon->couponCode()]);

        Assert::notNull($coupon, sprintf('Coupon with code %s has not been found.', $addCoupon->couponCode()));
        Assert::true($this->couponEligibilityChecker->isEligible($cart, $coupon));

        $cart->setPromotionCoupon($coupon);

        $this->orderProcessor->process($cart);
    }
}
