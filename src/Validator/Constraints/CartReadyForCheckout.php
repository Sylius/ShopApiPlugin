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

class CartReadyForCheckout extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.checkout.not_ready_for_checkout';

    /** @var string */
    public $messageOnNoAddress = 'sylius.shop_api.checkout.address_required';

    /** @var string */
    public $messageOnNoShippingCart = 'sylius.shop_api.checkout.shipping_required';

    /** @var string */
    public $messageOnNoPaymentCart = 'sylius.shop_api.checkout.payment_required';

    /** @inheritdoc */
    public function getTargets(): array
    {
        return [self::PROPERTY_CONSTRAINT];
    }

    /** @inheritdoc */
    public function validatedBy(): string
    {
        return 'sylius_shop_api_cart_ready_for_checkout';
    }
}
