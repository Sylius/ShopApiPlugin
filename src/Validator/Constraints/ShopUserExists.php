<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ShopUserExists extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.email.not_found';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'sylius_shop_api_shop_user_exists_validator';
    }
}
