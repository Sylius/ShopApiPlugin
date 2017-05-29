<?php

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Request\PutSimpleItemToCartRequest;
use Sylius\ShopApiPlugin\Request\PutVariantBasedConfigurableItemToCartRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PutItemToCartAction
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
    public function __invoke(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $commandRequest = $this->provideCommandRequest($request);

        $validationResults = $this->validator->validate($commandRequest);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST));
        }

        $this->bus->handle($commandRequest->getCommand());

        return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }

    /**
     * @param Request $request
     *
     * @return PutOptionBasedConfigurableItemToCartRequest|PutSimpleItemToCartRequest|PutVariantBasedConfigurableItemToCartRequest
     */
    private function provideCommandRequest(Request $request)
    {
        if (!$request->request->has('variantCode') && !$request->request->has('options')) {
            return new PutSimpleItemToCartRequest($request);
        }

        if ($request->request->has('variantCode') && !$request->request->has('options')) {
            return new PutVariantBasedConfigurableItemToCartRequest($request);
        }

        if (!$request->request->has('variantCode') && $request->request->has('options')) {
            return new PutOptionBasedConfigurableItemToCartRequest($request);
        }

        throw new NotFoundHttpException('Variant not found for given configuration');
    }
}
