<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\AddressBook;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\RemoveAddress;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactory;
use Sylius\ShopApiPlugin\Parser\CommandRequestParserInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Sylius\ShopApiPlugin\Request\RemoveAddressRequest;
use Sylius\ShopApiPlugin\Request\UserEmailBasedCommandRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RemoveAddressAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ValidationErrorViewFactory */
    private $validationErrorViewFactory;

    /** @var CommandBus */
    private $bus;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInUserProvider;

    /** @var CommandRequestParserInterface */
    private $commandRequestParser;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ValidatorInterface $validator,
        ValidationErrorViewFactory $validationErrorViewFactory,
        CommandBus $bus,
        LoggedInShopUserProviderInterface $loggedInUserProvider,
        CommandRequestParserInterface $commandRequestParser
    ) {
        $this->viewHandler = $viewHandler;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->bus = $bus;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->commandRequestParser = $commandRequestParser;
    }

    public function __invoke(Request $request): Response
    {
        try {
            /** @var ShopUserInterface $user */
            $user = $this->loggedInUserProvider->provide();
        } catch (TokenNotFoundException $exception) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }

        /** @var UserEmailBasedCommandRequestInterface $removeAddressRequest */
        $removeAddressRequest = $this->commandRequestParser->parse($request, RemoveAddress::class);
        $removeAddressRequest->setUserEmail($user->getEmail());

        $validationResults = $this->validator->validate($removeAddressRequest);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(
                View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST)
            );
        }

        if ($user->getCustomer() !== null) {
            $this->bus->handle($removeAddressRequest->getCommand());

            return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
        }

        return $this->viewHandler->handle(View::create(['message' => 'The user is not a customer'], Response::HTTP_BAD_REQUEST));
    }
}
