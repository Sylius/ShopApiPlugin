<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CartReadyForCheckout extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.cart.not_ready_for_checkout';

    /** {@inheritdoc} */
    public function getTargets(): array
    {
        return [self::PROPERTY_CONSTRAINT];
    }

    /** {@inheritdoc} */
    public function validatedBy(): string
    {
        return 'sylius_shop_api_cart_ready_for_checkout';
    }
}
