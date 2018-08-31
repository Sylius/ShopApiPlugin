<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Sylius\ShopApiPlugin\Command\EstimateShippingCost;
use Sylius\ShopApiPlugin\Request\EstimateShippingCostRequest;

final class EstimateShippingCostHandler
{
    /**
     * @var OrderRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var AddressFactoryInterface
     */
    private $addressFactory;

    /**
     * @var FactoryInterface
     */
    private $shipmentFactory;

    /**
     * @var ShippingMethodsResolverInterface
     */
    private $shippingMethodResolver;

    /**
     * @var ServiceRegistryInterface
     */
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

    /**
     * Handles the calculation of the shipping method
     *
     * @param EstimateShippingCost $estimateShippingCostRequest
     *
     * @throws UnresolvedDefaultShippingMethodException
     */
    public function handle(EstimateShippingCost $estimateShippingCostRequest)
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $estimateShippingCostRequest->cartToken()]);

        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setCountryCode($estimateShippingCostRequest->countryCode());
        $address->setProvinceCode($estimateShippingCostRequest->provinceCode());
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

        $calculator = $this->calculators->get($shippingMethod->getCalculator());

        $value = $calculator->calculate($shipment, $shippingMethod->getConfiguration());
        $currencyCode = $cart->getCurrencyCode();

        $estimateShippingCostRequest->setResult($value, $currencyCode);
    }
}
