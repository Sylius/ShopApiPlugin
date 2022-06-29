<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\ShopApiPlugin\Command\Cart\ChooseShippingMethod;
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

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory,
    ) {
        $this->orderRepository = $orderRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->eligibilityChecker = $eligibilityChecker;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function __invoke(ChooseShippingMethod $chooseShippingMethod): void
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
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);
    }
}
