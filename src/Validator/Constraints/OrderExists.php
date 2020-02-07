<?php

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
