<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\CompleteOrder;
use Sylius\ShopApiPlugin\Exception\NotLoggedInException;
use Sylius\ShopApiPlugin\Provider\LoggedInUserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class CompleteOrderAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var CommandBus */
    private $bus;

    /** @var LoggedInUserProviderInterface */
    private $loggedInUserProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        LoggedInUserProviderInterface $loggedInUserProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->loggedInUserProvider = $loggedInUserProvider;
    }

    public function __invoke(Request $request): Response
    {
        $email = $this->provideUserEmail($request);

        try {
            $this->bus->handle(
                new CompleteOrder(
                    $request->attributes->get('token'),
                    $email,
                    $request->request->get('notes')
                )
            );
        } catch (NotLoggedInException $notLoggedInException) {
            return $this->viewHandler->handle(
                View::create(
                    'You need to be logged in with the same user that wants to complete the order',
                    Response::HTTP_UNAUTHORIZED
                )
            );
        } catch (TokenNotFoundException $notLoggedInException) {
            return $this->viewHandler->handle(
                View::create(
                    'You need to be logged in with the same user that wants to complete the order',
                    Response::HTTP_UNAUTHORIZED
                )
            );
        }

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }

    private function provideUserEmail(Request $request): string
    {
        try {
            return $this->loggedInUserProvider->provide()->getEmail();
        } catch (TokenNotFoundException $tokenNotFoundException) {
            return $request->request->get('email');
        }
    }
}
