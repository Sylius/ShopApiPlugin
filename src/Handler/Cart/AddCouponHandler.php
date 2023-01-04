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
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\AddCoupon;
use Webmozart\Assert\Assert;

final class AddCouponHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var PromotionCouponRepositoryInterface */
    private $couponRepository;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var PromotionCouponEligibilityCheckerInterface */
    private $couponEligibilityChecker;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $couponRepository,
        OrderProcessorInterface $orderProcessor,
        PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker,
    ) {
        $this->orderRepository = $orderRepository;
        $this->couponRepository = $couponRepository;
        $this->orderProcessor = $orderProcessor;
        $this->couponEligibilityChecker = $couponEligibilityChecker;
    }

    public function __invoke(AddCoupon $addCoupon): void
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
