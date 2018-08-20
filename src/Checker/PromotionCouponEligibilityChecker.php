<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

final class PromotionCouponEligibilityChecker implements PromotionCouponEligibilityCheckerInterface
{
    /**
     * @var PromotionEligibilityCheckerInterface
     */
    private $promotionEligibilityChecker;

    /**
     * @var PromotionCouponEligibilityCheckerInterface
     */
    private $couponEligibilityChecker;

    /**
     * @param PromotionEligibilityCheckerInterface $promotionEligibilityChecker
     * @param PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker
     */
    public function __construct(
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker
    ) {
        $this->promotionEligibilityChecker = $promotionEligibilityChecker;
        $this->couponEligibilityChecker = $couponEligibilityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $cart, PromotionCouponInterface $coupon): bool
    {
        /** @var OrderInterface $cart */
        Assert::isInstanceOf($cart, OrderInterface::class);

        /** @var PromotionInterface $promotion */
        $promotion = $coupon->getPromotion();

        $cart->setPromotionCoupon($coupon);

        $isEligible =
            $promotion->hasChannel($cart->getChannel())
            && $this->couponEligibilityChecker->isEligible($cart, $coupon)
            && $this->promotionEligibilityChecker->isEligible($cart, $coupon->getPromotion())
        ;

        $cart->setPromotionCoupon(null);

        return $isEligible;
    }
}
