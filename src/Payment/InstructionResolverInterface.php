<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Payment;

use Sylius\Component\Core\Model\PaymentInterface;

interface InstructionResolverInterface
{
    const GATEWAY_OFFLINE = 'offline';
    const GATEWAY_HPP = 'hosted_payment_page';

    const TYPE_TEXT = 'text';
    const TYPE_REDIRECT = 'redirect';

    public function getInstruction(PaymentInterface $payment) : Instruction;
}
