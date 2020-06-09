<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Sylius\ShopApiPlugin\Validator\Cart\PaymentMethodExistsValidator;
use Symfony\Component\Validator\Constraint;

final class PaymentMethodExists extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.checkout.payment_method_does_not_exist';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return PaymentMethodExistsValidator::class;
    }
}
