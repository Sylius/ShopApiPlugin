<?php

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\ChangeItemQuantityRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ChangeItemQuantityAction
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ValidationErrorViewFactoryInterface
     */
    private $validationErrorViewFactory;

    /**
     * @param ViewHandlerInterface $viewHandler
     * @param CommandBus $bus
     * @param ValidatorInterface $validator
     * @param ValidationErrorViewFactoryInterface $validationErrorViewFactory
     */
    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $changeItemQuantityRequest = new ChangeItemQuantityRequest($request);

        $validationResults = $this->validator->validate($changeItemQuantityRequest);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST));
        }

        $this->bus->handle($changeItemQuantityRequest->getCommand());

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
