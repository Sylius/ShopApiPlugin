<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\CompleteOrder;
use Sylius\ShopApiPlugin\Exception\WrongUserException;
use Sylius\ShopApiPlugin\Parser\CommandRequestParserInterface;
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

    /** @var CommandRequestParserInterface */
    private $commandRequestParser;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        LoggedInShopUserProviderInterface $loggedInUserProvider,
        CommandRequestParserInterface $commandRequestParser
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->commandRequestParser = $commandRequestParser;
    }

    public function __invoke(Request $request): Response
    {
        $this->setDefaultEmailOnRequestIfNeeded($request);

        $commandRequest = $this->commandRequestParser->parse($request, CompleteOrder::class);

        try {
            $this->bus->handle($commandRequest->getCommand());
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

    private function setDefaultEmailOnRequestIfNeeded(Request $request): void
    {
        $defaultEmail = null;
        if ($this->loggedInUserProvider->isUserLoggedIn()) {
            $defaultEmail = $this->loggedInUserProvider->provide()->getEmail();
        }

        if (!$request->request->has('email')) {
            $request->request->set('email', $defaultEmail);
        }
    }
}
