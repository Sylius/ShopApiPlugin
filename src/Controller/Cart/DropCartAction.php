<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\CommandProvider\ValidationFailedExceptionInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DropCartAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var CommandBus */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CommandProviderInterface */
    private $dropCartCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CommandProviderInterface $dropCartCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->dropCartCommandProvider = $dropCartCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $this->bus->handle($this->dropCartCommandProvider->provide($request));

            return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
        } catch (ValidationFailedExceptionInterface $exception) {
            return $this->viewHandler->handle(View::create($this->validationErrorViewFactory->create($exception->getValidationErrors()), Response::HTTP_BAD_REQUEST));
        }
    }
}
