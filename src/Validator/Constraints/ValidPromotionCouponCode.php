<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ValidPromotionCouponCode extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.coupon.not_valid';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'sylius_shop_api_valid_coupon_code_validator';
    }
}
