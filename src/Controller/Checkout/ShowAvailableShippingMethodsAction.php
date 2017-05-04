<?php

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Sylius\ShopApiPlugin\View\ShipmentMethodView;
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
     * @var ViewHandlerInterface
     */
    private $shippingMethodsResolver;

    /**
     * @var ServiceRegistryInterface
     */
    private $calculators;

    /**
     * @param OrderRepositoryInterface $cartRepository
     * @param ViewHandlerInterface $viewHandler
     * @param ShippingMethodsResolverInterface $shippingMethodsResolver
     * @param ServiceRegistryInterface $calculators
     */
    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ViewHandlerInterface $viewHandler,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ServiceRegistryInterface $calculators
    ) {
        $this->cartRepository = $cartRepository;
        $this->viewHandler = $viewHandler;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->calculators = $calculators;
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
            /** @var CalculatorInterface $calculator */
            $calculator = $this->calculators->get($shippingMethod->getCalculator());

            $shippingMethodView = new ShipmentMethodView();

            $shippingMethodView->code = $shippingMethod->getCode();
            $shippingMethodView->name = $shippingMethod->getTranslation($locale)->getName();
            $shippingMethodView->description = $shippingMethod->getTranslation($locale)->getDescription();
            $shippingMethodView->price = $calculator->calculate($shipment, $shippingMethod->getConfiguration());

            $rawShippingMethods[$shippingMethodView->code] = $shippingMethodView;
        }

        return $rawShippingMethods;
    }
}
