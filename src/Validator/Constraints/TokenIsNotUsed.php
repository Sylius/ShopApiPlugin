<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class TokenIsNotUsed extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.token.already_taken';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'sylius_shop_api_token_is_not_used_validator';
    }
}
