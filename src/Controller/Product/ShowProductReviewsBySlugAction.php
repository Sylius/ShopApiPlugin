<?php

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\ViewRepository\ProductReviewsViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowProductReviewsBySlugAction
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

        $page = $this->productReviewsViewRepository->getByProductSlug(
            $request->attributes->get('slug'),
            $request->query->get('channel'),
            new PaginatorDetails($request->attributes->get('_route'), $request->query->all()),
            $request->query->get('locale')
        );

        return $this->viewHandler->handle(View::create($page, Response::HTTP_OK));
    }
}
