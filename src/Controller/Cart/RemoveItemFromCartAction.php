<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\RemoveItemFromCart;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Parser\CommandRequestParserInterface;
use Sylius\ShopApiPlugin\Request\RemoveItemFromCartRequest;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RemoveItemFromCartAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var CommandBus */
    private $bus;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CartViewRepositoryInterface */
    private $cartQuery;

    /** @var CommandRequestParserInterface */
    private $commandRequestParser;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CartViewRepositoryInterface $cartQuery,
        CommandRequestParserInterface $commandRequestParser
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->cartQuery = $cartQuery;
        $this->commandRequestParser = $commandRequestParser;
    }

    public function __invoke(Request $request): Response
    {
        $removeItemFromCartRequest = $this->commandRequestParser->parse($request, RemoveItemFromCart::class);

        $validationResults = $this->validator->validate($removeItemFromCartRequest);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST));
        }

        $removeItemFromCartCommand = $removeItemFromCartRequest->getCommand();
        assert($removeItemFromCartCommand instanceof RemoveItemFromCart);

        $this->bus->handle($removeItemFromCartCommand);

        try {
            return $this->viewHandler->handle(
                View::create($this->cartQuery->getOneByToken($removeItemFromCartCommand->orderToken()), Response::HTTP_OK)
            );
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
