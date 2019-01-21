<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\PutOptionBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Command\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Normalizer\RequestCartTokenNormalizerInterface;
use Sylius\ShopApiPlugin\Parser\CommandRequestParserInterface;
use Sylius\ShopApiPlugin\Request\CommandRequestInterface;
use Sylius\ShopApiPlugin\Request\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Request\PutSimpleItemToCartRequest;
use Sylius\ShopApiPlugin\Request\PutVariantBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
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

    /** @var RequestCartTokenNormalizerInterface */
    private $requestCartTokenNormalizer;

    /** @var CommandRequestParserInterface */
    private $commandRequestParser;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CartViewRepositoryInterface $cartQuery,
        RequestCartTokenNormalizerInterface $requestCartTokenNormalizer,
        CommandRequestParserInterface $commandRequestParser
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->cartQuery = $cartQuery;
        $this->requestCartTokenNormalizer = $requestCartTokenNormalizer;
        $this->commandRequestParser = $commandRequestParser;
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
        assert(
            $command instanceof PutSimpleItemToCart ||
            $command instanceof PutVariantBasedConfigurableItemToCart ||
            $command instanceof PutOptionBasedConfigurableItemToCart
        );

        $this->bus->handle($command);

        try {
            return $this->viewHandler->handle(
                View::create($this->cartQuery->getOneByToken($command->orderToken()), Response::HTTP_CREATED)
            );
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    private function provideCommandRequest(Request $request): CommandRequestInterface
    {
        $hasVariantCode = $request->request->has('variantCode');
        $hasOptionCode = $request->request->has('options');

        if (!$hasVariantCode && !$hasOptionCode) {
            return $this->commandRequestParser->parse($request, PutSimpleItemToCart::class);
        }

        if ($hasVariantCode && !$hasOptionCode) {
            return $this->commandRequestParser->parse($request, PutVariantBasedConfigurableItemToCart::class);
        }

        if (!$hasVariantCode && $hasOptionCode) {
            return $this->commandRequestParser->parse($request, PutOptionBasedConfigurableItemToCart::class);
        }

        throw new NotFoundHttpException('Variant not found for given configuration');
    }
}
