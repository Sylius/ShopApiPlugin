<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\AddressOrderRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AddressAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var CommandBus */
    private $bus;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus, ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
    }

    public function __invoke(Request $request): Response
    {
        $addressOrderRequest = new AddressOrderRequest($request);

        $validationResults = $this->validator->validate($addressOrderRequest);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST));
        }

        $addressOrderCommand = $addressOrderRequest->getCommand();

        $this->bus->handle($addressOrderCommand);

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
