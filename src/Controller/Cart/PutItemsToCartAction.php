<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Normalizer\RequestCartTokenNormalizerInterface;
use Sylius\ShopApiPlugin\Request\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Request\PutSimpleItemToCartRequest;
use Sylius\ShopApiPlugin\Request\PutVariantBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\View\ValidationErrorView;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PutItemsToCartAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var CommandBus */
    private $bus;

    /** @var ValidatorInterface */
    private $validator;

    /** @var CartViewRepositoryInterface */
    private $cartQuery;

    /** @var string */
    private $validationErrorViewClass;

    /** @var RequestCartTokenNormalizerInterface */
    private $requestCartTokenNormalizer;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        ValidatorInterface $validator,
        CartViewRepositoryInterface $cartQuery,
        RequestCartTokenNormalizerInterface $requestCartTokenNormalizer,
        string $validationErrorViewClass
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
        $this->cartQuery = $cartQuery;
        $this->requestCartTokenNormalizer = $requestCartTokenNormalizer;
        $this->validationErrorViewClass = $validationErrorViewClass;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ConstraintViolationListInterface[] $validationResults */
        $validationResults = [];
        $commandRequests = [];
        $commandsToExecute = [];

        try {
            $request = $this->requestCartTokenNormalizer->doNotAllowNullCartToken($request);
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        $token = $request->attributes->get('token');

        foreach ($request->request->get('items') as $item) {
            $item['token'] = $token;
            $commandRequests[] = $this->provideCommandRequest($item);
        }

        foreach ($commandRequests as $commandRequest) {
            $validationResult = $this->validator->validate($commandRequest);

            if (0 === count($validationResult)) {
                $commandsToExecute[] = $commandRequest->getCommand();
            }

            $validationResults[] = $validationResult;
        }

        if (!$this->isValid($validationResults)) {
            /** @var ValidationErrorView $errorMessage */
            $errorMessage = new $this->validationErrorViewClass();

            $errorMessage->code = Response::HTTP_BAD_REQUEST;
            $errorMessage->message = 'Validation failed';

            foreach ($validationResults as $validationResult) {
                $errors = [];

                /** @var ConstraintViolationInterface $result */
                foreach ($validationResult as $result) {
                    $errors[$result->getPropertyPath()][] = $result->getMessage();
                }

                $errorMessage->errors[] = $errors;
            }

            return $this->viewHandler->handle(View::create($errorMessage, Response::HTTP_BAD_REQUEST));
        }

        foreach ($commandsToExecute as $commandToExecute) {
            $this->bus->handle($commandToExecute);
        }

        try {
            return $this->viewHandler->handle(
                View::create($this->cartQuery->getOneByToken($token), Response::HTTP_CREATED)
            );
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    /** @return PutOptionBasedConfigurableItemToCartRequest|PutSimpleItemToCartRequest|PutVariantBasedConfigurableItemToCartRequest */
    private function provideCommandRequest(array $item)
    {
        $hasVariantCode = isset($item['variantCode']);
        $hasOptions = isset($item['options']);

        if (!$hasVariantCode && !$hasOptions) {
            return PutSimpleItemToCartRequest::fromArray($item);
        }

        if ($hasVariantCode && !$hasOptions) {
            return PutVariantBasedConfigurableItemToCartRequest::fromArray($item);
        }

        if (!$hasVariantCode && $hasOptions) {
            return PutOptionBasedConfigurableItemToCartRequest::fromArray($item);
        }

        throw new NotFoundHttpException('Variant not found for given configuration');
    }

    private function isValid(array $validationResults): bool
    {
        foreach ($validationResults as $validationResult) {
            if (0 !== count($validationResult)) {
                return false;
            }
        }

        return true;
    }
}
