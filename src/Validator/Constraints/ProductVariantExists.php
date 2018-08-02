<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ProductVariantExists extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.product_variant.exists';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'sylius_shop_api_product_variant_exists_validator';
    }
}
