<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CartNotEmpty extends Constraint
{
    /** @var string */
    public $emptyCartMessage = 'sylius.shop_api.checkout.cart.empty';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy(): string
    {
        return 'sylius_shop_api_cart_not_empty_validator';
    }
}
