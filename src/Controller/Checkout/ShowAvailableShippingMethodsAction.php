<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Sylius\ShopApiPlugin\Factory\ShippingMethodViewFactory;
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
     * @var ShippingMethodViewFactory
     */
    private $shippingMethodViewFactory;

    /**
     * @param OrderRepositoryInterface $cartRepository
     * @param ViewHandlerInterface $viewHandler
     * @param ShippingMethodsResolverInterface $shippingMethodsResolver
     * @param ShippingMethodViewFactory $shippingMethodViewFactory
     */
    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ViewHandlerInterface $viewHandler,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ShippingMethodViewFactory $shippingMethodViewFactory
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
    public function __invoke(Request $request): \Symfony\Component\HttpFoundation\Response
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
    private function getCalculatedShippingMethods(ShipmentInterface $shipment, string $locale): array
    {
        $rawShippingMethods = [];

        /** @var ShippingMethodInterface $shippingMethod */
        foreach ($this->shippingMethodsResolver->getSupportedMethods($shipment) as $shippingMethod) {

            $rawShippingMethods['methods'][$shippingMethod->getCode()] = $this->shippingMethodViewFactory->createWithShippingMethod($shipment, $shippingMethod, $locale);
        }

        return $rawShippingMethods;
    }
}
