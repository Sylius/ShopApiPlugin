<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\AddressBook;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactory;
use Sylius\ShopApiPlugin\Request\RemoveAddressRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RemoveAddressAction
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ValidationErrorViewFactory
     */
    private $validationErrorViewFactory;

    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @param ViewHandlerInterface $viewHandler
     * @param ValidatorInterface $validator
     * @param ValidationErrorViewFactory $validationErrorViewFactory
     * @param CommandBus $bus
     */
    public function __construct(
        ViewHandlerInterface $viewHandler,
        ValidatorInterface $validator,
        ValidationErrorViewFactory $validationErrorViewFactory,
        CommandBus $bus
    ) {
        $this->viewHandler = $viewHandler;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->bus = $bus;
    }

    public function __invoke(Request $request): Response
    {
        $removeAddressRequest = new RemoveAddressRequest($request);

        $validationResults = $this->validator->validate($removeAddressRequest);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST));
        }

        $this->bus->handle($removeAddressRequest->getCommand());

        return $this->viewHandler->handle(View::create('', Response::HTTP_NO_CONTENT));
    }
}
