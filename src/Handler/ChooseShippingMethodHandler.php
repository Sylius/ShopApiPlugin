<?php

namespace Sylius\ShopApiPlugin\Handler;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\ShopApiPlugin\Command\ChooseShippingMethod;

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
     * @param OrderRepositoryInterface $orderRepository
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param ShippingMethodEligibilityCheckerInterface $eligibilityChecker
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->eligibilityChecker = $eligibilityChecker;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * @param ChooseShippingMethod $chooseShippingMethod
     *
     * @throws \LogicException
     */
    public function handle(ChooseShippingMethod $chooseShippingMethod)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $chooseShippingMethod->orderToken()]);

        if (null === $order) {
            throw new \LogicException('Order has not been found');
        }

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        if (!$stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING)) {
            throw new \LogicException('Order cannot have shipment method assigned');
        }

        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $chooseShippingMethod->shippingMethod()]);

        if (null === $shippingMethod) {
            throw new \LogicException('Shipping method has not been found');
        }

        if (!isset($order->getShipments()[$chooseShippingMethod->shippingIdentifier()])) {
            throw new \LogicException('Shipping method has not been found');
        }

        $shipment = $order->getShipments()[$chooseShippingMethod->shippingIdentifier()];

        if (!$this->eligibilityChecker->isEligible($shipment, $shippingMethod)) {
            throw new \LogicException('Given shipment is not eligible for provided shipping method');
        }

        $shipment->setMethod($shippingMethod);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);
    }
}
