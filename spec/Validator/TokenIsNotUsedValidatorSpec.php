<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\SyliusShopApiPlugin\Validator\Constraints\TokenIsNotUsed;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class TokenIsNotUsedValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, OrderRepositoryInterface $orderRepository)
    {
        $this->beConstructedWith($orderRepository);

        $this->initialize($executionContext);
    }

    function it_does_not_add_constraint_if_order_exists(
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('ORDERTOKEN', new TokenIsNotUsed());
    }

    function it_adds_constraint_if_order_does_not_exits_exists(
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $executionContext->addViolation('sylius.shop_api.token.already_taken')->shouldBeCalled();

        $this->validate('ORDERTOKEN', new TokenIsNotUsed());
    }
}
