<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ProductInCartChannel extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.product.not_in_cart_channel';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy()
    {
        return 'sylius_shop_api_product_in_cart_channel_validator';
    }
}
