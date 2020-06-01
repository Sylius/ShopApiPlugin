<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CorrectPaymentMethod extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.cart.payment_method.valid';

    /** {@inheritdoc} */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy(): string
    {
        return 'sylius_shop_api_cart_correct_payment_method_selected';
    }
}
