<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Payment;

use Sylius\Component\Core\Model\PaymentInterface;

class InstructionResolver implements InstructionResolverInterface
{
    final public function getInstruction(PaymentInterface $payment) : Instruction
    {
        $method = $payment->getMethod();

        if (null === $method) {
            throw new \InvalidArgumentException('Payment method is not set.');
        }

        $gatewayConfig = $method->getGatewayConfig();

        $instruction = new Instruction();
        $instruction->gateway = InstructionResolverInterface::GATEWAY_OFFLINE;
        $instruction->type = InstructionResolverInterface::TYPE_TEXT;
        $instruction->content = $method->getInstructions();

        return $instruction;
    }
}
