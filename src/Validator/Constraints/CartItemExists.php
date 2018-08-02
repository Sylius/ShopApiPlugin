<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CartItemExists extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.cart_item.not_exists';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'sylius_shop_api_cart_item_exists_validator';
    }
}
