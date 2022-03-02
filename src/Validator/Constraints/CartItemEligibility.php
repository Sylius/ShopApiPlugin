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

final class CartItemEligibility extends Constraint
{
    /** @var string */
    public $nonEligibleProductMessage = 'sylius.shop_api.cart_item.product.non_eligible';

    /** @var string */
    public $nonEligibleProductVariantMessage = 'sylius.shop_api.cart_item.product_variant.non_eligible';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy()
    {
        return 'sylius_shop_api_cart_item_eligibility_validator';
    }
}
