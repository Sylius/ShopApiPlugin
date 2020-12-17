<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CartEligibility extends Constraint
{
    /** @var string */
    public $nonEligibleCartItemMessage = 'sylius.shop_api.checkout.cart_item.non_eligible';

    /** @var string */
    public $nonEligibleCartItemVariantMessage = 'sylius.shop_api.checkout.cart_item_variant.non_eligible';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy(): string
    {
        return 'sylius_shop_api_cart_eligibility_validator';
    }
}
