<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\SyliusShopApiPlugin\ViewRepository\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SummarizeAction
{
    /**
     * @var CartViewRepositoryInterface
     */
    private $cartQuery;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @param CartViewRepositoryInterface $cartQuery
     * @param ViewHandlerInterface $viewHandler
     */
    public function __construct(
        CartViewRepositoryInterface $cartQuery,
        ViewHandlerInterface $viewHandler
    ) {
        $this->cartQuery = $cartQuery;
        $this->viewHandler = $viewHandler;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        try {
            return $this->viewHandler->handle(
                View::create($this->cartQuery->getOneByToken($request->attributes->get('token')), Response::HTTP_OK)
            );
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
