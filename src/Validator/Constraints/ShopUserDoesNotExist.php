<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ShopUserDoesNotExist extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.email.unique';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'sylius_shop_api_shop_user_does_not_exist_validator';
    }
}
