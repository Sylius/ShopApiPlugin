<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\AddressOrder as AddressShipmentCommand;
use Sylius\ShopApiPlugin\Mapper\AddressMapperInterface;
use Sylius\ShopApiPlugin\Model\Address;

final class AddressOrderHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        AddressMapperInterface $addressMapper,
        FactoryInterface $stateMachineFactory
    ): void {
        $this->beConstructedWith($orderRepository, $addressMapper, $stateMachineFactory);
    }

    function it_handles_order_shipment_addressing(
        AddressMapperInterface $addressMapper,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress,
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine
    ): void {
        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $shippingAddressData = Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
            'company' => 'Detective Inc',
            'phoneNumber' => '999',
        ]);
        $addressMapper->map($shippingAddressData)->willReturn($shippingAddress);

        $billingAddressData = Address::createFromArray([
            'firstName' => 'John',
            'lastName' => 'Watson',
            'city' => 'London City',
            'street' => 'Baker Street 21b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
            'company' => 'Detective Corp',
            'phoneNumber' => '111',
        ]);
        $addressMapper->map($billingAddressData)->willReturn($billingAddress);

        $order->setShippingAddress($shippingAddress)->shouldBeCalled();
        $order->setBillingAddress($billingAddress)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('address')->willReturn(true);
        $stateMachine->apply('address')->shouldBeCalled();

        $this(new AddressShipmentCommand( 'ORDERTOKEN', $shippingAddressData, $billingAddressData));
    }

    function it_does_not_create_new_addresses_for_already_addressed_order(
        AddressMapperInterface $addressMapper,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress,
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine
    ): void {
        $order->getShippingAddress()->willReturn($shippingAddress);
        $order->getBillingAddress()->willReturn($billingAddress);

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $shippingAddressData = Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
            'company' => 'Detective Inc',
            'phoneNumber' => '999',
        ]);
        $addressMapper->mapExisting($shippingAddress, $shippingAddressData)->willReturn($shippingAddress);

        $billingAddressData = Address::createFromArray([
            'firstName'    => 'John',
            'lastName'     => 'Watson',
            'city'         => 'London City',
            'street'       => 'Baker Street 21b',
            'countryCode'  => 'GB',
            'postcode'     => 'NWB',
            'provinceName' => 'Greater London',
            'company'      => 'Detective Corp',
            'phoneNumber'  => '111',
        ]);
        $addressMapper->mapExisting($billingAddress, $billingAddressData)->willReturn($billingAddress);

        $order->setShippingAddress($shippingAddress)->shouldBeCalled();
        $order->setBillingAddress($billingAddress)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('address')->willReturn(true);
        $stateMachine->apply('address')->shouldBeCalled();

        $this(new AddressShipmentCommand('ORDERTOKEN', $shippingAddressData,$billingAddressData));
    }

    function it_throws_an_exception_if_order_does_not_exist(
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\LogicException::class)->during('__invoke', [
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
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('address')->willReturn(false);

        $this->shouldThrow(\LogicException::class)->during('__invoke', [
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
