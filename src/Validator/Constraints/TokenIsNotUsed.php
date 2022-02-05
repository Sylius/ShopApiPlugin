<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
