<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class TokenIsNotUsed extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.cart.token_already_taken';

    /** {@inheritdoc} */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy(): string
    {
        return 'sylius_shop_api_token_is_not_used_validator';
    }
}
