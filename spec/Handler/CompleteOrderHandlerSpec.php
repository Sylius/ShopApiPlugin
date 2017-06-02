<?php

namespace spec\Sylius\ShopApiPlugin\Handler;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Command\CompleteOrder;
use Sylius\ShopApiPlugin\Handler\CompleteOrderHandler;

final class CompleteOrderHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        StateMachineFactoryInterface $stateMachineFactory
    ) {
        $this->beConstructedWith($orderRepository, $customerRepository, $customerFactory, $stateMachineFactory);
    }

    function it_handles_order_completion_for_existing_customer(
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $customerRepository->findOneBy(['email' => 'example@customer.com'])->willReturn($customer);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);

        $order->setNotes(null)->shouldBeCalled();
        $order->setCustomer($customer)->shouldBeCalled();
        $stateMachine->apply('complete')->shouldBeCalled();

        $this->handle(new CompleteOrder('ORDERTOKEN', 'example@customer.com'));
    }

    function it_handles_order_completion_for_new_customer(
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $customerRepository->findOneBy(['email' => 'example@customer.com'])->willReturn(null);
        $customerFactory->createNew()->willReturn($customer);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);

        $order->setNotes(null)->shouldBeCalled();
        $order->setCustomer($customer)->shouldBeCalled();
        $stateMachine->apply('complete')->shouldBeCalled();
        $customerRepository->add($customer)->shouldBeCalled();

        $this->handle(new CompleteOrder('ORDERTOKEN', 'example@customer.com'));
    }

    function it_handles_order_completion_with_notes(
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $customerRepository->findOneBy(['email' => 'example@customer.com'])->willReturn($customer);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);

        $order->setNotes('Some notes')->shouldBeCalled();
        $order->setCustomer($customer)->shouldBeCalled();
        $stateMachine->apply('complete')->shouldBeCalled();

        $this->handle(new CompleteOrder('ORDERTOKEN', 'example@customer.com', 'Some notes'));
    }

    function it_throws_an_exception_if_order_does_not_exist(OrderRepositoryInterface $orderRepository)
    {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new CompleteOrder('ORDERTOKEN', 'example@customer.com')]);
    }

    function it_throws_an_exception_if_order_cannot_be_addressed(
        StateMachineFactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new CompleteOrder('ORDERTOKEN', 'example@customer.com')]);
    }
}
