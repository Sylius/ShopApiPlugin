<?php

namespace spec\Sylius\ShopApiPlugin\Payment;

use Payum\Core\Model\GatewayConfigInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\ShopApiPlugin\Payment\Instruction;
use Sylius\ShopApiPlugin\Payment\InstructionResolver;
use Sylius\ShopApiPlugin\Payment\InstructionResolverInterface;

class InstructionResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(InstructionResolver::class);
    }

    function it_implements_instruction_resolver_interface()
    {
        $this->shouldImplement(InstructionResolverInterface::class);
    }

    function it_returns_text_content_for_offline_payments(
        PaymentInterface $payment,
        PaymentMethodInterface $method,
        GatewayConfigInterface $gatewayConfig
    ) {
        $payment->getMethod()->willReturn($method);
        $method->getGatewayConfig()->willReturn($gatewayConfig);
        $method->getInstructions()->willReturn('Please make bank transfer to PL1234 1234 1234 1234.');
        $gatewayConfig->getFactoryName()->willReturn(InstructionResolverInterface::GATEWAY_OFFLINE);

        $expectedInstruction = new Instruction();
        $expectedInstruction->gateway = InstructionResolverInterface::GATEWAY_OFFLINE;
        $expectedInstruction->type = InstructionResolverInterface::TYPE_TEXT;
        $expectedInstruction->content = 'Please make bank transfer to PL1234 1234 1234 1234.';

        $this->getInstruction($payment)->shouldBeLike($expectedInstruction);
    }

    function it_throws_an_exception_when_method_is_not_set(
        PaymentInterface $payment
    ) {
        $payment->getMethod()->willReturn(null);

        $this
            ->shouldThrow(new \InvalidArgumentException('Payment method is not set.'))
            ->during('getInstruction', [$payment])
        ;
    }
}
