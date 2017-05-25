<?php

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CartWithGivenTokenExists extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.shop_api.cart.not_exists';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_shop_api_cart_with_given_token_does_not_exists_validator';
    }
}
