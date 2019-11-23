<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Command\Cart\CompleteOrderWithCustomer;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;

final class CompleteOrderWithCustomerHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        OrderProcessorInterface $orderProcessor,
        CustomerProviderInterface $customerProvider
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $stateMachineFactory,
            $orderProcessor,
            $customerProvider
        );
    }

    function it_handles_order_completion_with_customer_assignment(
        OrderInterface $order,
        CustomerProviderInterface $customerProvider,
        CustomerInterface $customer,
        OrderRepositoryInterface $orderRepository,
        OrderProcessorInterface $orderProcessor,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $customerProvider->provide('john@doe.com')->willReturn($customer);

        $order->setCustomer($customer)->shouldBeCalled();
        $orderProcessor->process($order)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);

        $order->setNotes(null)->shouldBeCalled();
        $stateMachine->apply('complete')->shouldBeCalled();

        $this(new CompleteOrderWithCustomer('ORDERTOKEN', 'john@doe.com'));
    }

    function it_handles_order_completion_with_notes(
        OrderInterface $order,
        CustomerProviderInterface $customerProvider,
        CustomerInterface $customer,
        OrderRepositoryInterface $orderRepository,
        OrderProcessorInterface $orderProcessor,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $customerProvider->provide('john@doe.com')->willReturn($customer);

        $order->setCustomer($customer)->shouldBeCalled();
        $orderProcessor->process($order)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);

        $order->setNotes('Some notes')->shouldBeCalled();
        $stateMachine->apply('complete')->shouldBeCalled();

        $this(new CompleteOrderWithCustomer('ORDERTOKEN', 'john@doe.com', 'Some notes'));
    }

    function it_throws_an_exception_if_order_does_not_exist(
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompleteOrderWithCustomer('ORDERTOKEN', 'john@doe.com')])
        ;
    }

    function it_throws_an_exception_if_order_cannot_be_completed(
        StateMachineFactoryInterface $stateMachineFactory,
        CustomerProviderInterface $customerProvider,
        CustomerInterface $customer,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        OrderProcessorInterface $orderProcessor,
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $customerProvider->provide('john@doe.com')->willReturn($customer);

        $order->setCustomer($customer)->shouldBeCalled();
        $orderProcessor->process($order)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE)->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompleteOrderWithCustomer('ORDERTOKEN', 'john@doe.com')])
        ;
    }
}
