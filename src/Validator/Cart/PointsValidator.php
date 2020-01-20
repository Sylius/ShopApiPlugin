<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PointsValidator extends ConstraintValidator
{

    protected $percentage = 0.50;

    private $tokenStorage;
    private $orderRepository;
    private $translator;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        OrderRepositoryInterface $orderRepository,
        TranslatorInterface $translator
    ) {
        $this->orderRepository = $orderRepository;
        $this->tokenStorage    = $tokenStorage;
        $this->translator      = $translator;
    }

    public function validate($request, Constraint $constraint)
    {
        $customer = null;
        if ($this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
            if ($this->tokenStorage->getToken()->getUser()->getCustomer() instanceof CustomerInterface) {
                $customer = $this->tokenStorage->getToken()->getUser()->getCustomer();
            }
        }
        $amount = $request->getPoints();

        if ($customer === null && $amount) {
            $this->buildViolation($constraint, $this->translator->trans('sylius.shop_api.points.login_required'));
        }

        if ($customer && $amount) {
            /** @var OrderInterface|null $cart */
            $cart = $this->orderRepository->findOneBy([
                    'tokenValue' => $request->getToken(),
                    'state'      => OrderInterface::STATE_CART
                ]
            );
            $customer = $this->tokenStorage->getToken()->getUser()->getCustomer();

            if ($cart->getPromotionCoupon()) {
                $this->buildViolation($constraint,
                    $this->translator->trans('sylius.shop_api.points.already_using_coupon')
                );

                return;
            }

            $itemsTotals = [];
            foreach ($cart->getItems() as $item) {
                $itemsTotals[] = $item->getTotal();
            }
            $customerPoints = $customer->getCustomerPoint();
            $maxPoints      = (int) round(array_sum($itemsTotals) * $this->percentage);

            if ( ! $customerPoints || ! $customerPoints->getPoints()) {
                $this->buildViolation($constraint, $this->translator->trans('sylius.shop_api.points.not_have'));

                return;
            }

            if ( ! ($amount <= $customerPoints->getPoints())) {
                $this->buildViolation($constraint, $this->translator->trans('sylius.shop_api.points.not_enough'));

                return;
            }

            if ( ! ($amount <= $maxPoints)) {
                $maxPoints /= 100;
                $amount    /= 100;
                $this->buildViolation($constraint,
                    $this->translator->trans('sylius.shop_api.points.too_much',
                        ['amount' => $amount, 'maxPoints' => $maxPoints]
                    )
                );

                return;
            }

            if ($customerPoints && $amount && $amount <= $customerPoints->getPoints() && $amount <= $maxPoints) {
                return;
            }
            $this->buildViolation($constraint, $this->translator->trans('sylius.shop_api.points.error'));
        }
    }

    /** @param Constraint $constraint */
    private function buildViolation(Constraint $constraint, $message)
    {
        $this->context->buildViolation($message)->atPath('points')->addViolation();
    }
}
