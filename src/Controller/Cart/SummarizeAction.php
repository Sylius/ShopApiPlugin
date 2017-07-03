<?php

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Query\CartQueryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SummarizeAction
{
    /**
     * @var CartQueryInterface
     */
    private $cartQuery;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @param CartQueryInterface $cartQuery
     * @param ViewHandlerInterface $viewHandler
     */
    public function __construct(
        CartQueryInterface $cartQuery,
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
                View::create($this->cartQuery->findByToken($request->attributes->get('token')), Response::HTTP_OK)
            );
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
