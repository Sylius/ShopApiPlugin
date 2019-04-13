<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Normalizer\RequestCartTokenNormalizerInterface;
use Sylius\ShopApiPlugin\Request\Cart\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Request\Cart\PutSimpleItemToCartRequest;
use Sylius\ShopApiPlugin\Request\Cart\PutVariantBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PutItemToCartAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CartViewRepositoryInterface */
    private $cartQuery;

    /** @var RequestCartTokenNormalizerInterface */
    private $requestCartTokenNormalizer;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CartViewRepositoryInterface $cartQuery,
        RequestCartTokenNormalizerInterface $requestCartTokenNormalizer
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->cartQuery = $cartQuery;
        $this->requestCartTokenNormalizer = $requestCartTokenNormalizer;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $request = $this->requestCartTokenNormalizer->doNotAllowNullCartToken($request);
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        $commandRequest = $this->provideCommandRequest($request);

        $validationResults = $this->validator->validate($commandRequest);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(
                View::create($this->validationErrorViewFactory->create($validationResults),
                    Response::HTTP_BAD_REQUEST
                )
            );
        }

        $command = $commandRequest->getCommand();
        $this->bus->dispatch($command);

        try {
            return $this->viewHandler->handle(
                View::create($this->cartQuery->getOneByToken($command->orderToken()), Response::HTTP_CREATED)
            );
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    /** @return PutOptionBasedConfigurableItemToCartRequest|PutSimpleItemToCartRequest|PutVariantBasedConfigurableItemToCartRequest */
    private function provideCommandRequest(Request $request)
    {
        $hasVariantCode = $request->request->has('variantCode');
        $hasOptionCode = $request->request->has('options');

        if (!$hasVariantCode && !$hasOptionCode) {
            return PutSimpleItemToCartRequest::fromRequest($request);
        }

        if ($hasVariantCode && !$hasOptionCode) {
            return PutVariantBasedConfigurableItemToCartRequest::fromRequest($request);
        }

        if (!$hasVariantCode && $hasOptionCode) {
            return PutOptionBasedConfigurableItemToCartRequest::fromRequest($request);
        }

        throw new NotFoundHttpException('Variant not found for given configuration');
    }
}
