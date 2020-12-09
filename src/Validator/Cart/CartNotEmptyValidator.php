<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartNotEmpty;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartNotEmptyValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $_cartRepository;

    /**
     * CartNotEmptyValidator constructor.
     */
    public function __construct(OrderRepositoryInterface $cartRepository)
    {
        $this->_cartRepository = $cartRepository;
    }

    /**
     * @param mixed      $token
     */
    public function validate($token, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, CartNotEmpty::class);

        $cart = $this->_cartRepository->findOneBy(
            [
                'tokenValue' => $token,
                'state' => OrderInterface::STATE_CART,
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
