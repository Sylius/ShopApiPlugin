<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use SM\Factory\FactoryInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\ShopApiPlugin\Command\ChooseShippingMethod;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

final class ChooseShippingMethodHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ShippingMethodRepositoryInterface */
    private $shippingMethodRepository;

    /** @var ShippingMethodEligibilityCheckerInterface */
    private $eligibilityChecker;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->orderRepository = $orderRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->eligibilityChecker = $eligibilityChecker;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /** @param ChooseShippingMethod $chooseShippingMethod */
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
        Assert::true(isset($cart->getShipments()[$chooseShippingMethod->shipmentIdentifier()]), 'Can not find shipment with given identifier.');

        $shipment = $cart->getShipments()[$chooseShippingMethod->shipmentIdentifier()];

        Assert::true($this->eligibilityChecker->isEligible($shipment, $shippingMethod), 'Given shipment is not eligible for provided shipping method.');

        $shipment->setMethod($shippingMethod);

        $this->eventDispatcher->dispatch('sylius.order.pre_select_shipping', new ResourceControllerEvent($cart));

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        $this->eventDispatcher->dispatch('sylius.order.post_select_shipping', new ResourceControllerEvent($cart));
    }
}
