<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartEmpty;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartEmptyValidator extends ConstraintValidator
{
    /**
     * @var OrderRepositoryInterface
     */
    private $_cartRepository;

    /**
     * CartEmptyValidator constructor.
     *
     * @param OrderRepositoryInterface $cartRepository
     */
    public function __construct(OrderRepositoryInterface $cartRepository)
    {
        $this->_cartRepository = $cartRepository;
    }

    /**
     * @param mixed      $token
     * @param Constraint $constraint
     *
     * @return void
     */
    public function validate($token, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, CartEmpty::class);

        $cart = $this->_cartRepository->findOneBy(
            [
                'tokenValue' => $token,
                'state' => OrderInterface::STATE_CART
            ]
        );

        if ($cart === null) {
            return;
        }

        Assert::isInstanceOf($cart, OrderInterface::class);

        if ($cart->getItems()->isEmpty()) {
            $this->context->addViolation(
                $constraint->emptyCartMessage
            );
        }
    }
}
