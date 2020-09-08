<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use App\Entity\Order\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PointsExistsInCartConstraintValidator extends ConstraintValidator
{

    private $translator;
    private $orderRepository;

    public function __construct(
        TranslatorInterface $translator,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->translator = $translator;
    }

    public function validate($request, Constraint $constraint)
    {
        /** @var Order|null $cart */
        $cart = $this->orderRepository->findOneBy([
                'tokenValue' => $request->getToken(),
                'state'      => OrderInterface::STATE_CART
            ]
        );

        if ($cart->getPoints()) {
            $this->buildViolation($constraint,
                $this->translator->trans('sylius.shop_api.points.already_using_coupon')
            );

            return;
        }
    }

    /** @param Constraint $constraint */
    private function buildViolation(Constraint $constraint, $message)
    {
        $this->context->buildViolation($message)->atPath('points')->addViolation();
    }
}
