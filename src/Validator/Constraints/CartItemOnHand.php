<?php

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CartItemOnHand extends Constraint
{

    /** @var string */
    public $message = 'sylius.shop_api.cart_item.on_hand';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy()
    {
        return 'sylius_shop_api_cart_item_on_hand_validator';
    }
}
