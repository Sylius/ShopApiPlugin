<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class PhoneConstraint extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.phone.not_valid';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy()
    {
        return 'sylius_shop_api_phone_validator';
    }
}
