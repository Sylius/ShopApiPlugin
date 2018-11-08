<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ValidPromotionCouponCodeValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker
    ): void {
        $this->beConstructedWith($orderRepository, $promotionCouponRepository, $couponEligibilityChecker);

        $this->initialize($executionContext);
    }

    // todo: add more tests ad this is the only test that works on the request itself
}
