<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Command\Cart\RemoveCoupon;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

final class RemoveCouponAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CartViewRepositoryInterface */
    private $cartQuery;

    /** @var CommandProviderInterface */
    private $removeCouponCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CartViewRepositoryInterface $cartQuery,
        CommandProviderInterface $removeCouponCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->cartQuery = $cartQuery;
        $this->removeCouponCommandProvider = $removeCouponCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        $validationResults = $this->removeCouponCommandProvider->validate($request);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        /** @var RemoveCoupon $removeCouponCommand */
        $removeCouponCommand = $this->removeCouponCommandProvider->getCommand($request);

        $this->bus->dispatch($removeCouponCommand);

        try {
            return $this->viewHandler->handle(View::create(
                $this->cartQuery->getOneByToken($removeCouponCommand->orderToken()),
                Response::HTTP_OK
            ));
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
