<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\AddressOrder as AddressShipmentCommand;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AddressOrderHandlerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, AddressFactoryInterface $addressFactory, FactoryInterface $stateMachineFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith($orderRepository, $addressFactory, $stateMachineFactory, $eventDispatcher);
    }

    function it_handles_order_shipment_addressing(
        AddressFactoryInterface $addressFactory,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress,
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        EventDispatcherInterface $eventDispatcher
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $addressFactory->createNew()->willReturn($shippingAddress, $billingAddress);

        $shippingAddress->setFirstName('Sherlock')->shouldBeCalled();
        $shippingAddress->setLastName('Holmes')->shouldBeCalled();
        $shippingAddress->setCity('London')->shouldBeCalled();
        $shippingAddress->setStreet('Baker Street 221b')->shouldBeCalled();
        $shippingAddress->setCountryCode('GB')->shouldBeCalled();
        $shippingAddress->setPostcode('NWB')->shouldBeCalled();
        $shippingAddress->setProvinceName('Greater London')->shouldBeCalled();

        $billingAddress->setFirstName('John')->shouldBeCalled();
        $billingAddress->setLastName('Watson')->shouldBeCalled();
        $billingAddress->setCity('London City')->shouldBeCalled();
        $billingAddress->setStreet('Baker Street 21b')->shouldBeCalled();
        $billingAddress->setCountryCode('GB')->shouldBeCalled();
        $billingAddress->setPostcode('NWB')->shouldBeCalled();
        $billingAddress->setProvinceName('Greater London')->shouldBeCalled();

        $order->setShippingAddress($shippingAddress)->shouldBeCalled();
        $order->setBillingAddress($billingAddress)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('address')->willReturn(true);

        $eventDispatcher->dispatch('sylius.order.pre_address', new ResourceControllerEvent($order->getWrappedObject()))->shouldBeCalled();
        $stateMachine->apply('address')->shouldBeCalled();
        $eventDispatcher->dispatch('sylius.order.post_address', new ResourceControllerEvent($order->getWrappedObject()))->shouldBeCalled();

        $this->handle(new AddressShipmentCommand(
            'ORDERTOKEN',
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
                'firstName' => 'John',
                'lastName' => 'Watson',
                'city' => 'London City',
                'street' => 'Baker Street 21b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ])
        ));
    }

    function it_throws_an_exception_if_order_does_not_exist(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        EventDispatcherInterface $eventDispatcher
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $eventDispatcher->dispatch(Argument::any())->shouldNotBeCalled();
        $stateMachine->apply('address')->shouldNotBeCalled();

        $this->shouldThrow(\LogicException::class)->during('handle', [
            new AddressShipmentCommand(
                'ORDERTOKEN',
                Address::createFromArray([
                    'firstName' => 'Sherlock',
                    'lastName' => 'Holmes',
                    'city' => 'London',
                    'street' => 'Baker Street 221b',
                    'countryCode' => 'GB',
                    'postcode' => 'NWB',
                    'provinceName' => 'Greater London',
                ]),
                Address::createFromArray([
                    'firstName' => 'John',
                    'lastName' => 'Watson',
                    'city' => 'London City',
                    'street' => 'Baker Street 21b',
                    'countryCode' => 'GB',
                    'postcode' => 'NWB',
                    'provinceName' => 'Greater London',
                ])
            ),
        ]);
    }

    function it_throws_an_exception_if_order_cannot_be_addressed(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        EventDispatcherInterface $eventDispatcher
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('address')->willReturn(false);

        $eventDispatcher->dispatch(Argument::any())->shouldNotBeCalled();
        $stateMachine->apply('address')->shouldNotBeCalled();

        $this->shouldThrow(\LogicException::class)->during('handle', [
            new AddressShipmentCommand(
                'ORDERTOKEN',
                Address::createFromArray([
                    'firstName' => 'Sherlock',
                    'lastName' => 'Holmes',
                    'city' => 'London',
                    'street' => 'Baker Street 221b',
                    'countryCode' => 'GB',
                    'postcode' => 'NWB',
                    'provinceName' => 'Greater London',
                ]),
                Address::createFromArray([
                    'firstName' => 'John',
                    'lastName' => 'Watson',
                    'city' => 'London City',
                    'street' => 'Baker Street 21b',
                    'countryCode' => 'GB',
                    'postcode' => 'NWB',
                    'provinceName' => 'Greater London',
                ])
            ),
        ]);
    }
}
