<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CartExists extends Constraint
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
        return 'sylius_shop_api_cart_exists_validator';
    }
}
