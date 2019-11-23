<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Exception\OrderTotalIntegrityException;
use Sylius\ShopApiPlugin\Exception\WrongUserException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final class CompleteOrderAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var CommandProviderInterface */
    private $assignCustomerToCartCommandProvider;

    /** @var CommandProviderInterface */
    private $completeOrderCommandProvider;

    /** @var CommandProviderInterface */
    private $completeOrderWithCustomerCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        CommandProviderInterface $assignCustomerToCartCommandProvider,
        CommandProviderInterface $completeOrderCommandProvider,
        CommandProviderInterface $completeOrderWithCustomerCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->assignCustomerToCartCommandProvider = $assignCustomerToCartCommandProvider;
        $this->completeOrderCommandProvider = $completeOrderCommandProvider;
        $this->completeOrderWithCustomerCommandProvider = $completeOrderWithCustomerCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $this->bus->dispatch($this->provideCorrectCompleteOrderCommand($request));
        } catch (HandlerFailedException $exception) {
            $previousException = $exception->getPrevious();

            if ($previousException instanceof WrongUserException) {
                return $this->viewHandler->handle(
                    View::create(
                        'You need to be logged in with the same user that wants to complete the order',
                        Response::HTTP_UNAUTHORIZED
                    )
                );
            }

            if ($previousException instanceof OrderTotalIntegrityException) {
                return $this->viewHandler->handle(
                    View::create(
                        ['code' => Response::HTTP_BAD_REQUEST, 'message' => $exception->getMessage()],
                        Response::HTTP_BAD_REQUEST
                    )
                );
            }

            throw $exception;
        }

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }

    private function provideCorrectCompleteOrderCommand(Request $request): CommandInterface
    {
        if (null !== $request->request->get('email')) {
            return $this->completeOrderWithCustomerCommandProvider->getCommand($request);
        }

        return $this->completeOrderCommandProvider->getCommand($request);
    }
}
