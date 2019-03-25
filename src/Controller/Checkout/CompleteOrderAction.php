<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\Cart\CompleteOrder;
use Sylius\ShopApiPlugin\Exception\WrongUserException;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CompleteOrderAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var CommandBus */
    private $bus;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInUserProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        LoggedInShopUserProviderInterface $loggedInUserProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->loggedInUserProvider = $loggedInUserProvider;
    }

    public function __invoke(Request $request): Response
    {
        if ($this->loggedInUserProvider->isUserLoggedIn()) {
            $defaultEmail = $this->loggedInUserProvider->provide()->getEmail();
        }

        try {
            $this->bus->handle(
                new CompleteOrder(
                    $request->attributes->get('token'),
                    $request->request->get('email', $defaultEmail ?? null),
                    $request->request->get('notes')
                )
            );
        } catch (WrongUserException $notLoggedInException) {
            return $this->viewHandler->handle(
                View::create(
                    'You need to be logged in with the same user that wants to complete the order',
                    Response::HTTP_UNAUTHORIZED
                )
            );
        }

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
