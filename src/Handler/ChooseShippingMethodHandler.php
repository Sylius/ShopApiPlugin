<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Doctrine\Common\Collections\Collection;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Sylius\ShopApiPlugin\Command\ChooseShippingMethod;
use Webmozart\Assert\Assert;

final class ChooseShippingMethodHandler
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ShippingMethodRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var ShippingMethodEligibilityCheckerInterface
     */
    private $eligibilityChecker;

    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var ShippingMethodsResolverInterface
     */
    private $shippingMethodsResolver;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param ShippingMethodEligibilityCheckerInterface $eligibilityChecker
     * @param ShippingMethodsResolverInterface $shippingMethodsResolver
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        FactoryInterface $stateMachineFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->eligibilityChecker = $eligibilityChecker;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
    }

    /**
     * @param ChooseShippingMethod $chooseShippingMethod
     */
    public function handle(ChooseShippingMethod $chooseShippingMethod)
    {
        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $chooseShippingMethod->orderToken()]);

        Assert::notNull($cart, 'Cart has not been found.');

        $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);

        Assert::true($stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING), 'Order cannot have shipment method assigned.');

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $chooseShippingMethod->shippingMethod()]);

        Assert::notNull($shippingMethod, 'Shipping method has not been found');

        $shipmentsAvailable = $this->getShipmentsAvailable($cart->getShipments());

        Assert::true(isset($shipmentsAvailable[$chooseShippingMethod->shipmentIdentifier()]), 'Can not find shipment with given identifier.');

        $shipment = $shipmentsAvailable[$chooseShippingMethod->shipmentIdentifier()];

        Assert::true($this->eligibilityChecker->isEligible($shipment, $shippingMethod), 'Given shipment is not eligible for provided shipping method.');

        $shipment->setMethod($shippingMethod);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);
    }

    /**
     * @param Collection $shipments
     *
     * @return array
     */
    private function getShipmentsAvailable(Collection $shipments): array
    {
        $shipmentsAvailable = [];
        foreach ($shipments as $shipment) {
            /** @var ShippingMethodInterface $shippingMethod */
            foreach ($this->shippingMethodsResolver->getSupportedMethods($shipment) as $shippingMethod) {
                $shipmentsAvailable[$shippingMethod->getCode()] = $shipment;
            }
        }

        return $shipmentsAvailable;
    }
}
