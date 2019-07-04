<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Encoder\GuestOrderJWTEncoderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\Customer\GuestLoginRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GuestLoginAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var GuestOrderJWTEncoderInterface */
    private $guestOrderJWTEncoder;

    /**
     * GuestLoginAction constructor.
     *
     * @param ValidatorInterface $validator
     * @param ValidationErrorViewFactoryInterface $validationErrorViewFactory
     * @param ViewHandlerInterface $viewHandler
     * @param OrderRepositoryInterface $orderRepository
     * @param GuestOrderJWTEncoderInterface $guestOrderJWTEncoder
     */
    public function __construct(
        ViewHandlerInterface $viewHandler,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        OrderRepositoryInterface $orderRepository,
        GuestOrderJWTEncoderInterface $guestOrderJWTEncoder
    ) {
        $this->viewHandler                = $viewHandler;
        $this->validator                  = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->orderRepository            = $orderRepository;
        $this->guestOrderJWTEncoder       = $guestOrderJWTEncoder;
    }

    public function __invoke(Request $request): Response
    {
        // This is just to validate that all necessary fields are present.
        $loginRequest     = new GuestLoginRequest($request);
        $validationErrors = $this->validator->validate($loginRequest);
        if (0 < count($validationErrors)) {
            return $this->viewHandler->handle(
                View::create(
                    $this->validationErrorViewFactory->create($validationErrors),
                    Response::HTTP_BAD_REQUEST
                )
            );
        }

        // Actual login logic
        $success = false;

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($loginRequest->getOrderNumber());

        // The order has to exist and must be placed
        if (null !== $order && $order->getCheckoutState() !== OrderCheckoutStates::STATE_CART) {
            /** @var CustomerInterface $customer */
            $customer      = $order->getCustomer();
            $paymentMethod = $order->getLastPayment()->getMethod();

            // The order must be a guest order. Also the provided email & payment method must match.
            if (
                null === $customer->getUser() &&
                $loginRequest->getEmail() === $customer->getEmail() &&
                $paymentMethod->getCode() === $loginRequest->getPaymentMethodCode()
            ) {
                $success = true;
            }
        }

        // Return the jwt on success
        if (true === $success) {
            $jwt = $this->guestOrderJWTEncoder->encode($order);

            return $this->viewHandler->handle(View::create(['jwt' => $jwt], Response::HTTP_OK));
        }

        return $this->viewHandler->handle(View::create(['message' => 'Bad credentials.'], Response::HTTP_UNAUTHORIZED));
    }
}
