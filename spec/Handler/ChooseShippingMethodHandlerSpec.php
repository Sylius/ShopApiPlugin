<?php

namespace spec\Sylius\ShopApiPlugin\Handler;

use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\ShopApiPlugin\Command\ChooseShippingMethod;
use Sylius\ShopApiPlugin\Handler\ChooseShippingMethodHandler;
use PhpSpec\ObjectBehavior;

final class ChooseShippingMethodHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory
    ) {
        $this->beConstructedWith($orderRepository, $shippingMethodRepository, $eligibilityChecker, $stateMachineFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChooseShippingMethodHandler::class);
    }

    function it_assignes_choosen_shipping_method_to_specified_shipment(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodInterface $shippingMethod,
        ShipmentInterface $shipment,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getShipments()->willReturn([$shipment]);
        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethod);

        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(true);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_shipping')->willReturn(true);

        $shipment->setMethod($shippingMethod)->shouldBeCalled();
        $stateMachine->apply('select_shipping')->shouldBeCalled();

        $this->handle(new ChooseShippingMethod('ORDERTOKEN', 0, 'DHL_SHIPPING_METHOD'));
    }

    function it_throws_an_exception_if_shipping_method_is_not_eligible(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodInterface $shippingMethod,
        ShipmentInterface $shipment,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getShipments()->willReturn([$shipment]);
        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethod);

        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(false);
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_shipping')->willReturn(true);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply('select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [
                new ChooseShippingMethod('ORDERTOKEN', 0, 'DHL_SHIPPING_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_order_with_given_token_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        ShipmentInterface $shipment
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [
                new ChooseShippingMethod('ORDERTOKEN', 0, 'DHL_SHIPPING_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_order_cannot_have_shipping_selected(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentInterface $shipment,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn(null);
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_shipping')->willReturn(false);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply('select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [
                new ChooseShippingMethod('ORDERTOKEN', 0, 'DHL_SHIPPING_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_shipping_method_with_given_code_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentInterface $shipment,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn(null);
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_shipping')->willReturn(true);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply('select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [
                new ChooseShippingMethod('ORDERTOKEN', 0, 'DHL_SHIPPING_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_ordered_shipment_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodInterface $shippingMethod,
        ShipmentInterface $shipment,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethod);
        $order->getShipments()->willReturn([]);
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_shipping')->willReturn(true);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply('select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [
                new ChooseShippingMethod('ORDERTOKEN', 0, 'DHL_SHIPPING_METHOD'),
            ])
        ;
    }
}
