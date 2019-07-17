<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\ShippingMethodViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowAvailableShippingMethodsAction
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ShippingMethodsResolverInterface */
    private $shippingMethodsResolver;

    /** @var ShippingMethodViewFactoryInterface */
    private $shippingMethodViewFactory;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ViewHandlerInterface $viewHandler,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ShippingMethodViewFactoryInterface $shippingMethodViewFactory,
        FactoryInterface $stateMachineFactory
    ) {
        $this->cartRepository = $cartRepository;
        $this->viewHandler = $viewHandler;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->shippingMethodViewFactory = $shippingMethodViewFactory;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given token does not exist!');
        }

        if (!$this->isCheckoutTransitionPossible($cart, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING)) {
            throw new BadRequestHttpException('The shipment methods cannot be resolved in the current state of cart!');
        }

        $shipments = [];
        foreach ($cart->getShipments() as $shipment) {
            $shipments['shipments'][] = $this->getCalculatedShippingMethods($shipment, $cart->getLocaleCode());
        }

        return $this->viewHandler->handle(View::create($shipments));
    }

    private function getCalculatedShippingMethods(ShipmentInterface $shipment, string $locale): array
    {
        $rawShippingMethods = [];

        /** @var ShippingMethodInterface $shippingMethod */
        foreach ($this->shippingMethodsResolver->getSupportedMethods($shipment) as $shippingMethod) {
            /** @var OrderInterface $order */
            $order = $shipment->getOrder();

            $rawShippingMethods['methods'][$shippingMethod->getCode()] = $this->shippingMethodViewFactory->createWithShippingMethod(
                $shipment,
                $shippingMethod,
                $locale,
                $order->getCurrencyCode()
            );
        }

        return $rawShippingMethods;
    }

    private function isCheckoutTransitionPossible(OrderInterface $cart, string $transition): bool
    {
        return $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->can($transition);
    }
}
