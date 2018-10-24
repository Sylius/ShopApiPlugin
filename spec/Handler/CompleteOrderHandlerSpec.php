<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\CompleteOrder;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;

final class CompleteOrderHandlerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, CustomerProviderInterface $customerProvider, StateMachineFactoryInterface $stateMachineFactory): void
    {
        $this->beConstructedWith($orderRepository, $customerProvider, $stateMachineFactory);
    }

    function it_handles_order_completion_for_existing_customer(
        CustomerInterface $customer,
        CustomerProviderInterface $customerProvider,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $customerProvider->provide('example@customer.com')->willReturn($customer);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);

        $order->setNotes(null)->shouldBeCalled();
        $order->setCustomer($customer)->shouldBeCalled();
        $stateMachine->apply('complete')->shouldBeCalled();

        $this->handle(new CompleteOrder('ORDERTOKEN', 'example@customer.com'));
    }

    function it_handles_order_completion_with_notes(
        CustomerInterface $customer,
        CustomerProviderInterface $customerProvider,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $customerProvider->provide('example@customer.com')->willReturn($customer);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);

        $order->setNotes('Some notes')->shouldBeCalled();
        $order->setCustomer($customer)->shouldBeCalled();
        $stateMachine->apply('complete')->shouldBeCalled();

        $this->handle(new CompleteOrder('ORDERTOKEN', 'example@customer.com', 'Some notes'));
    }

    function it_throws_an_exception_if_order_does_not_exist(OrderRepositoryInterface $orderRepository): void
    {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new CompleteOrder('ORDERTOKEN', 'example@customer.com')]);
    }

    function it_throws_an_exception_if_order_cannot_be_addressed(
        StateMachineFactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new CompleteOrder('ORDERTOKEN', 'example@customer.com')]);
    }
}
