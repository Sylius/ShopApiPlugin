<?php

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Sylius\ShopApiPlugin\Validator\Cart\PointsExistsInCartConstraintValidator;

class PointsExistsInCartConstraint extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.points.already_using_coupon';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy()
    {
        return 'sylius_shop_api_points_exists_in_cart_validator';
    }
}
