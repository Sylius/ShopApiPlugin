<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ValidPromotionCouponCode extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.shop_api.coupon.not_valid';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_shop_api_valid_coupon_code_validator';
    }
}
