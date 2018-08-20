<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Request\PutSimpleItemToCartRequest;
use Sylius\ShopApiPlugin\Request\PutVariantBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\ViewRepository\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PutItemToCartAction
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

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CartViewRepositoryInterface $cartQuery
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->cartQuery = $cartQuery;
    }

    public function __invoke(Request $request): Response
    {
        $commandRequest = $this->provideCommandRequest($request);

        $validationResults = $this->validator->validate($commandRequest);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST));
        }

        $command = $commandRequest->getCommand();
        $this->bus->handle($command);

        try {
            return $this->viewHandler->handle(
                View::create($this->cartQuery->getOneByToken($command->orderToken()), Response::HTTP_CREATED)
            );
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    private function provideCommandRequest(Request $request)
    {
        if (!$request->request->has('variantCode') && !$request->request->has('options')) {
            return PutSimpleItemToCartRequest::fromRequest($request);
        }

        if ($request->request->has('variantCode') && !$request->request->has('options')) {
            return PutVariantBasedConfigurableItemToCartRequest::fromRequest($request);
        }

        if (!$request->request->has('variantCode') && $request->request->has('options')) {
            return PutOptionBasedConfigurableItemToCartRequest::fromRequest($request);
        }

        throw new NotFoundHttpException('Variant not found for given configuration');
    }
}
