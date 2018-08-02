<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Sylius\ShopApiPlugin\Request\AddCouponRequest;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ValidPromotionCouponCodeValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var PromotionCouponRepositoryInterface */
    private $promotionCouponRepository;

    /** @var PromotionCouponEligibilityCheckerInterface */
    private $couponEligibilityChecker;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        PromotionCouponEligibilityCheckerInterface $couponEligibilityChecker
    ) {
        $this->orderRepository = $orderRepository;
        $this->promotionCouponRepository = $promotionCouponRepository;
        $this->couponEligibilityChecker = $couponEligibilityChecker;
    }

    public function validate($request, Constraint $constraint): void
    {
        /** @var AddCouponRequest $request */
        Assert::isInstanceOf($request, AddCouponRequest::class);

        try {
            $command = $request->getCommand();
        } catch (\InvalidArgumentException $exception) {
            $this->buildViolation($constraint);

            return;
        }

        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $command->orderToken(), 'state' => OrderInterface::STATE_CART]);

        if (null === $cart) {
            return;
        }

        /** @var PromotionCouponInterface $coupon */
        $coupon = $this->promotionCouponRepository->findOneBy(['code' => $command->couponCode()]);

        if (null === $coupon || !$this->couponEligibilityChecker->isEligible($cart, $coupon)) {
            $this->buildViolation($constraint);

            return;
        }
    }

    /**
     * @param Constraint $constraint
     */
    private function buildViolation(Constraint $constraint)
    {
        $this->context
            ->buildViolation($constraint->message)
            ->atPath('coupon')
            ->addViolation()
        ;
    }
}
