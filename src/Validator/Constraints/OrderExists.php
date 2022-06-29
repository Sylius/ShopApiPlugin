<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\ShopApiPlugin\Validator\Order\OrderExistsValidator;
use Symfony\Component\Validator\Constraint;

final class OrderExists extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.order.not_exists';

    /** @var array|string[] */
    public $state = [OrderInterface::STATE_NEW, OrderInterface::STATE_FULFILLED, OrderInterface::STATE_CANCELLED];

    public function validatedBy(): string
    {
        return OrderExistsValidator::class;
    }
}
