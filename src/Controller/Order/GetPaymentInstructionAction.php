<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Order;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Payum\Core\Model\GatewayConfigInterface;
use Payum\Core\Payum;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Generic;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\RegisterCustomerRequest;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GetPaymentInstructionAction
{
    /**
     * @var Payum
     */
    private $payum;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    public function __construct(
        Payum $payum,
        OrderRepositoryInterface $orderRepository,
        ViewHandlerInterface $viewHandler
    ) {
        $this->payum = $payum;
        $this->orderRepository = $orderRepository;
        $this->viewHandler = $viewHandler;
    }

    public function __invoke(Request $request): Response
    {
        $token = $request->attributes->get('token');

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByTokenValue($token);

        if (null === $order) {
            throw new NotFoundHttpException(sprintf('Order with token "%s" does not exist.', $token));
        }

        $payment = $order->getLastPayment(PaymentInterface::STATE_NEW);

        if (null === $payment) {
            throw new \LogicException(sprintf('Order with token "%s" does not have any "new" payments.', $token));
        }

        $method = $payment->getMethod();
        $gatewayConfig = $method->getGatewayConfig();

        $token = $this->provideTokenBasedOnPayment($payment);

        if ('offline' === $gatewayConfig->getFactoryName()) {
            $view = View::create([
                'method' => $gatewayConfig->getGatewayName(),
                'type' => 'text',
                'content' => $method->getInstructions(),
            ]);

            return $this->viewHandler->handle($view);
        }

        $gateway = $this->payum->getGateway($token->getGatewayName());
        $gateway->execute(new Capture($token));
        $this->payum->getHttpRequestVerifier()->invalidate($token);

        $view = View::create([
            'method' => $gatewayConfig->getGatewayName(),
            'type' => 'redirect',
            'content' => [
                'url' => $token->getTargetUrl(),
            ]
        ]);

        return $this->viewHandler->handle($view);
    }

    private function provideTokenBasedOnPayment(PaymentInterface $payment): TokenInterface
    {
        $method = $payment->getMethod();
        $gatewayConfig = $method->getGatewayConfig();

        $token = $this->getTokenFactory()->createCaptureToken(
            $gatewayConfig->getGatewayName(),
            $payment,
            'sylius_shop_homepage'
        );

        return $token;
    }

    private function getTokenFactory(): GenericTokenFactoryInterface
    {
        return $this->payum->getTokenFactory();
    }
}
