<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;

final class PromotionCouponEligibilityCheckerSpec extends ObjectBehavior
{
    function let(
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker
    ): void {
        $this->beConstructedWith($promotionEligibilityChecker, $couponEligibilityChecker);
    }

    function it_is_promotion_coupon_eligiblity_checker(): void
    {
        $this->shouldImplement(PromotionCouponEligibilityCheckerInterface::class);
    }

    function it_confirms_that_Coupon_can_be_used_on_cart(
        ChannelInterface $channel,
        PromotionCouponInterface $coupon,
        PromotionInterface $promotion,
        OrderInterface $order,
        PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker,
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker
    ): void {
        $coupon->getPromotion()->willReturn($promotion);
        $order->getChannel()->willReturn($channel);
        $promotion->hasChannel($channel)->willReturn(true);

        $order->setPromotionCoupon($coupon)->shouldBeCalled();
        $order->setPromotionCoupon(null)->shouldBeCalled();

        $couponEligibilityChecker->isEligible($order, $coupon)->willReturn(true);
        $promotionEligibilityChecker->isEligible($order, $promotion)->willReturn(true);

        $this->isEligible($order, $coupon)->shouldReturn(true);
    }

    function it_checks_if_order_is_not_eligible_for_coupon(
        ChannelInterface $channel,
        PromotionCouponInterface $coupon,
        PromotionInterface $promotion,
        OrderInterface $order,
        PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker
    ): void {
        $coupon->getPromotion()->willReturn($promotion);
        $order->getChannel()->willReturn($channel);
        $promotion->hasChannel($channel)->willReturn(true);

        $order->setPromotionCoupon($coupon)->shouldBeCalled();
        $order->setPromotionCoupon(null)->shouldBeCalled();

        $couponEligibilityChecker->isEligible($order, $coupon)->willReturn(false);

        $this->isEligible($order, $coupon)->shouldReturn(false);
    }

    function it_checks_if_order_is_not_eligible_for_coupons_promotion(
        ChannelInterface $channel,
        PromotionCouponInterface $coupon,
        PromotionInterface $promotion,
        OrderInterface $order,
        PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker,
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker
    ): void {
        $coupon->getPromotion()->willReturn($promotion);
        $order->getChannel()->willReturn($channel);
        $promotion->hasChannel($channel)->willReturn(true);

        $order->setPromotionCoupon($coupon)->shouldBeCalled();
        $order->setPromotionCoupon(null)->shouldBeCalled();

        $couponEligibilityChecker->isEligible($order, $coupon)->willReturn(true);
        $promotionEligibilityChecker->isEligible($order, $promotion)->willReturn(false);

        $this->isEligible($order, $coupon)->shouldReturn(false);
    }

    function it_checks_if_coupons_promotion_is_not_enabled_for_channel(
        ChannelInterface $channel,
        PromotionCouponInterface $coupon,
        PromotionInterface $promotion,
        OrderInterface $order
    ): void {
        $coupon->getPromotion()->willReturn($promotion);
        $order->getChannel()->willReturn($channel);
        $promotion->hasChannel($channel)->willReturn(false);

        $order->setPromotionCoupon($coupon)->shouldBeCalled();
        $order->setPromotionCoupon(null)->shouldBeCalled();

        $this->isEligible($order, $coupon)->shouldReturn(false);
    }
}
