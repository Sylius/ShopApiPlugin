<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CartCheckoutItemEligibility extends Constraint
{
    /** @var string */
    public $messageOnNonEligibleCartItem = 'sylius.shop_api.checkout.cart_item.non_eligible';

    /** @var string */
    public $messageOnNonEligibleCartItemVariant = 'sylius.shop_api.checkout.cart_item_variant.non_eligible';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy(): string
    {
        return 'sylius_shop_api_cart_checkout_item_eligibility_validator';
    }
}
