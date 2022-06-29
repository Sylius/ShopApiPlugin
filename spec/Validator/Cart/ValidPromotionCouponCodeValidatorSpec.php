<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker,
    ): void {
        $this->beConstructedWith($orderRepository, $promotionCouponRepository, $couponEligibilityChecker);

        $this->initialize($executionContext);
    }

    // todo: add more tests ad this is the only test that works on the request itself
}
