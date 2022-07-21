<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class SimpleProduct extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.product.not_simple';

    /** @inheritdoc */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /** @inheritdoc */
    public function validatedBy()
    {
        return 'sylius_shop_api_simple_product_validator';
    }
}
