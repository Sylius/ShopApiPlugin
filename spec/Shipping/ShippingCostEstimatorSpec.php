<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Shipping;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Sylius\ShopApiPlugin\Shipping\ShippingCost;
use Sylius\ShopApiPlugin\Shipping\ShippingCostEstimatorInterface;

final class ShippingCostEstimatorSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        AddressFactoryInterface $addressFactory,
        FactoryInterface $shipmentFactory,
        ShippingMethodsResolverInterface $shippingMethodResolver,
        ServiceRegistryInterface $calculators,
    ): void {
        $this->beConstructedWith(
            $cartRepository,
            $addressFactory,
            $shipmentFactory,
            $shippingMethodResolver,
            $calculators,
        );
    }

    function it_implements_interface(): void
    {
        $this->shouldImplement(ShippingCostEstimatorInterface::class);
    }

    function it_finds_a_shipping_method_for_this_address(
        OrderRepositoryInterface $cartRepository,
        OrderInterface $cart,
        AddressInterface $address,
        AddressFactoryInterface $addressFactory,
        FactoryInterface $shipmentFactory,
        ShipmentInterface $shipment,
        ShippingMethodsResolverInterface $shippingMethodResolver,
        ShippingMethodInterface $shippingMethod,
        ServiceRegistryInterface $calculators,
        CalculatorInterface $calculator,
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'TOKEN'])->shouldBeCalled()->willReturn($cart);

        $addressFactory->createNew()->shouldBeCalled()->willReturn($address);
        $address->setCountryCode('DE')->shouldBeCalled();
        $address->setProvinceCode('de_ND')->shouldBeCalled();

        $cart->setShippingAddress($address)->shouldBeCalled();

        $shipmentFactory->createNew()->shouldBeCalled()->willReturn($shipment);
        $shipment->setOrder($cart);

        $shippingMethodResolver->getSupportedMethods($shipment)->willReturn([$shippingMethod]);

        $shippingMethod->getCalculator()->shouldBeCalled()->willReturn('estimated_shipping');
        $calculators->get('estimated_shipping')->shouldBeCalled()->willReturn($calculator);

        $shipmentMethodConfig = [];
        $shippingMethod->getConfiguration()->shouldBeCalled()->willReturn($shipmentMethodConfig);

        $calculator->calculate($shipment, $shipmentMethodConfig)->shouldBeCalled()->willReturn(100);
        $cart->getCurrencyCode()->shouldBeCalled()->willReturn('EU');

        $cart->setShippingAddress(null)->shouldBeCalled();

        $this->estimate('TOKEN', 'DE', 'de_ND')
             ->shouldBeLike(new ShippingCost(100, 'EU'))
        ;
    }

    function it_throws_an_exception_if_there_is_no_shipping_method(
        OrderRepositoryInterface $cartRepository,
        OrderInterface $cart,
        AddressInterface $address,
        AddressFactoryInterface $addressFactory,
        FactoryInterface $shipmentFactory,
        ShipmentInterface $shipment,
        ShippingMethodsResolverInterface $shippingMethodResolver,
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'TOKEN'])->shouldBeCalled()->willReturn($cart);

        $addressFactory->createNew()->shouldBeCalled()->willReturn($address);
        $address->setCountryCode('DE')->shouldBeCalled();
        $address->setProvinceCode('de_ND')->shouldBeCalled();

        $cart->setShippingAddress($address)->shouldBeCalled();

        $shipmentFactory->createNew()->shouldBeCalled()->willReturn($shipment);
        $shipment->setOrder($cart);

        $shippingMethodResolver->getSupportedMethods($shipment)->willReturn([]);

        $this
            ->shouldThrow(UnresolvedDefaultShippingMethodException::class)
            ->during('estimate', ['TOKEN', 'DE', 'de_ND'])
        ;
    }
}
