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

final class PointsValidator extends ConstraintValidator
{

    protected $percentage = 50;

    private $tokenStorage;
    private $orderRepository;

    public function __construct(TokenStorageInterface $tokenStorage, OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->tokenStorage    = $tokenStorage;
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
            $this->context->addViolation('Login required');
        }

        if ($customer && $amount) {
            /** @var OrderInterface|null $cart */
            $cart     = $this->orderRepository->findOneBy([
                    'tokenValue' => $request->getToken(),
                    'state'      => OrderInterface::STATE_CART
                ]
            );
            $customer = $this->tokenStorage->getToken()->getUser()->getCustomer();

            if ($cart->getPromotionCoupon()) {
                $this->context->addViolation('Unable to use coupons and points');

                return;
            }

            $itemsTotals = [];
            foreach ($cart->getItems() as $item) {
                $itemsTotals[] = $item->getTotal();
            }
            $customerPoints = $customer->getCustomerPoint();
            $maxPoints      = (int) round(array_sum($itemsTotals) * $this->percentage);

            if ( ! $customerPoints || ! $customerPoints->getPoints()) {
                $this->context->addViolation('Customer does not have enough points');

                return;
            }

            if ( ! ($amount <= $customerPoints->getPoints())) {
                $this->context->addViolation('Not enough customer bonuses');

                return;
            }

            if ( ! ($amount <= $maxPoints)) {
                $maxPoints /= 100;
                $amount    /= 100;
                $this->context->addViolation("It is possible to use {$amount}р bonuses for this order, max {$maxPoints}р"
                );

                return;
            }

            if ($customerPoints && $amount && $amount <= $customerPoints->getPoints() && $amount <= $maxPoints) {
                return;
            }
            $this->context->addViolation("undefined error");
        }
    }

}
