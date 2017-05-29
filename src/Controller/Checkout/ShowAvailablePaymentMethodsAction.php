<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Factory\PaymentMethodFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Sylius\ShopApiPlugin\Factory\PaymentMethodViewFactoryInterface;
use Sylius\ShopApiPlugin\View\PaymentMethodView;
use Sylius\ShopApiPlugin\View\ShipmentMethodView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShowAvailablePaymentMethodsAction
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
     * @var PaymentMethodsResolverInterface
     */
    private $paymentMethodsResolver;

    /**
     * @var PaymentMethodViewFactoryInterface
     */
    private $paymentMethodViewFactory;

    /**
     * @param OrderRepositoryInterface $cartRepository
     * @param ViewHandlerInterface $viewHandler
     * @param PaymentMethodsResolverInterface $paymentMethodResolver
     * @param PaymentMethodViewFactoryInterface $paymentMethodViewFactory
     */
    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ViewHandlerInterface $viewHandler,
        PaymentMethodsResolverInterface $paymentMethodResolver,
        PaymentMethodViewFactoryInterface $paymentMethodViewFactory
    ) {
        $this->cartRepository = $cartRepository;
        $this->viewHandler = $viewHandler;
        $this->paymentMethodsResolver = $paymentMethodResolver;
        $this->paymentMethodViewFactory = $paymentMethodViewFactory;
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

        $payments = [];

        foreach ($cart->getPayments() as $payment) {
            $payments['payments'][] = $this->getPaymentMethods($payment, $cart->getLocaleCode());
        }

        return $this->viewHandler->handle(View::create($payments));
    }

    /**
     * @param PaymentInterface $payment
     * @param string $locale
     *
     * @return array
     */
    private function getPaymentMethods(PaymentInterface $payment, string $locale): array
    {
        $rawPaymentMethods = [];

        /** @var PaymentMethodInterface $paymentMethod */
        foreach ($this->paymentMethodsResolver->getSupportedMethods($payment) as $paymentMethod) {
            $rawPaymentMethods['methods'][$paymentMethod->getCode()] = $this->paymentMethodViewFactory->create($paymentMethod, $locale);
        }

        return $rawPaymentMethods;
    }
}
