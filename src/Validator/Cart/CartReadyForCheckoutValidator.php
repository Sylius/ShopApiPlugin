<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartReadyForCheckout;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CartReadyForCheckoutValidator extends ConstraintValidator
{
    /** @var RepositoryInterface */
    private $cartRepository;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    public function __construct(RepositoryInterface $cartRepository, FactoryInterface $stateMachineFactory)
    {
        $this->cartRepository = $cartRepository;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /** {@inheritdoc} */
    public function validate($value, Constraint $constraint): void
    {
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $value]);
        if ($cart === null) {
            return;
        }

        $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);

        if (!$stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE)) {
            /** @var CartReadyForCheckout $constraint */
            $this->context->addViolation($constraint->message);
        }
    }
}
