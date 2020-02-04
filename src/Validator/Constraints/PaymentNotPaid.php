<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\ShopApiPlugin\Validator\Order\PaymentNotPaidValidator;
use Symfony\Component\Validator\Constraint;

final class PaymentNotPaid extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.payment.paid';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return PaymentNotPaidValidator::class;
    }
}
