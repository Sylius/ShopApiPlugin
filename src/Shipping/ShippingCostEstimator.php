<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Shipping;

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
use Webmozart\Assert\Assert;

final class ShippingCostEstimator implements ShippingCostEstimatorInterface
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var AddressFactoryInterface */
    private $addressFactory;

    /** @var FactoryInterface */
    private $shipmentFactory;

    /** @var ShippingMethodsResolverInterface */
    private $shippingMethodResolver;

    /** @var ServiceRegistryInterface */
    private $calculators;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        AddressFactoryInterface $addressFactory,
        FactoryInterface $shipmentFactory,
        ShippingMethodsResolverInterface $shippingMethodResolver,
        ServiceRegistryInterface $calculators
    ) {
        $this->cartRepository = $cartRepository;
        $this->addressFactory = $addressFactory;
        $this->shipmentFactory = $shipmentFactory;
        $this->shippingMethodResolver = $shippingMethodResolver;
        $this->calculators = $calculators;
    }

    public function estimate(
        string $cartToken,
        string $countryCode,
        ?string $provinceCode
    ): ShippingCost {
        /** @var OrderInterface|null $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $cartToken]);
        Assert::notNull($cart);

        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setCountryCode($countryCode);
        $address->setProvinceCode($provinceCode);
        $cart->setShippingAddress($address);

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentFactory->createNew();
        $shipment->setOrder($cart);

        /** @var ShippingMethodInterface[] $shippingMethods */
        $shippingMethods = $this->shippingMethodResolver->getSupportedMethods($shipment);

        if (empty($shippingMethods)) {
            throw new UnresolvedDefaultShippingMethodException();
        }

        $shippingMethod = $shippingMethods[0];

        $calculatorName = $shippingMethod->getCalculator();
        Assert::notNull($calculatorName);

        /** @var CalculatorInterface $calculator */
        $calculator = $this->calculators->get($calculatorName);

        $value = $calculator->calculate($shipment, $shippingMethod->getConfiguration());
        $currencyCode = $cart->getCurrencyCode();

        // Unsetting the shipping address because it causes errors when saving the cart
        $cart->setShippingAddress(null);

        return new ShippingCost($value, $currencyCode);
    }
}
