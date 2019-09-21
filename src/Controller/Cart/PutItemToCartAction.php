<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Command\Cart\PutOptionBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Command\Cart\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Normalizer\RequestCartTokenNormalizerInterface;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

final class PutItemToCartAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CartViewRepositoryInterface */
    private $cartQuery;

    /** @var RequestCartTokenNormalizerInterface */
    private $requestCartTokenNormalizer;

    /** @var CommandProviderInterface */
    private $putItemToCartCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CartViewRepositoryInterface $cartQuery,
        RequestCartTokenNormalizerInterface $requestCartTokenNormalizer,
        CommandProviderInterface $putItemToCartCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->cartQuery = $cartQuery;
        $this->requestCartTokenNormalizer = $requestCartTokenNormalizer;
        $this->putItemToCartCommandProvider = $putItemToCartCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $request = $this->requestCartTokenNormalizer->doNotAllowNullCartToken($request);
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        $validationResults = $this->putItemToCartCommandProvider->validate($request);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        /** @var PutOptionBasedConfigurableItemToCart|PutSimpleItemToCart|PutVariantBasedConfigurableItemToCart $command */
        $command = $this->putItemToCartCommandProvider->getCommand($request);
        $this->bus->dispatch($command);

        try {
            return $this->viewHandler->handle(View::create(
                $this->cartQuery->getOneByToken($command->orderToken()),
                Response::HTTP_CREATED
            ));
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
