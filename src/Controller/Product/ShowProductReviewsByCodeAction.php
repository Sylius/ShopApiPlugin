<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\SyliusShopApiPlugin\Model\PaginatorDetails;
use Sylius\SyliusShopApiPlugin\ViewRepository\ProductReviewsViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowProductReviewsByCodeAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ProductReviewsViewRepositoryInterface */
    private $productReviewsViewRepository;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ProductReviewsViewRepositoryInterface $productReviewsViewRepository
    ) {
        $this->viewHandler = $viewHandler;
        $this->productReviewsViewRepository = $productReviewsViewRepository;
    }

    public function __invoke(Request $request): Response
    {
        if (!$request->query->has('channel')) {
            throw new NotFoundHttpException('Cannot find product without channel provided');
        }

        $page = $this->productReviewsViewRepository->getByProductCode(
            $request->attributes->get('code'),
            $request->query->get('channel'),
            new PaginatorDetails($request->attributes->get('_route'), $request->query->all())
        );

        return $this->viewHandler->handle(View::create($page, Response::HTTP_OK));
    }
}
