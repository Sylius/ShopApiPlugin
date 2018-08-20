<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Sylius\SyliusShopApiPlugin\Factory\ShippingMethodViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShowAvailableShippingMethodsAction
{
    /**
     * @var OrderRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var ShippingMethodsResolverInterface
     */
    private $shippingMethodsResolver;

    /**
     * @var ShippingMethodViewFactoryInterface
     */
    private $shippingMethodViewFactory;

    /**
     * @param OrderRepositoryInterface $cartRepository
     * @param ViewHandlerInterface $viewHandler
     * @param ShippingMethodsResolverInterface $shippingMethodsResolver
     * @param ShippingMethodViewFactoryInterface $shippingMethodViewFactory
     */
    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ViewHandlerInterface $viewHandler,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ShippingMethodViewFactoryInterface $shippingMethodViewFactory
    ) {
        $this->cartRepository = $cartRepository;
        $this->viewHandler = $viewHandler;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->shippingMethodViewFactory = $shippingMethodViewFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        $shipments = [];

        foreach ($cart->getShipments() as $shipment) {
            $shipments['shipments'][] = $this->getCalculatedShippingMethods($shipment, $cart->getLocaleCode());
        }

        return $this->viewHandler->handle(View::create($shipments));
    }

    /**
     * @param ShipmentInterface $shipment
     * @param string $locale
     *
     * @return array
     */
    private function getCalculatedShippingMethods(ShipmentInterface $shipment, $locale)
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
}
