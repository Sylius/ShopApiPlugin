<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\SyliusShopApiPlugin\Model\PaginatorDetails;
use Sylius\SyliusShopApiPlugin\ViewRepository\ProductCatalogViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowProductCatalogByTaxonCodeAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ProductCatalogViewRepositoryInterface */
    private $productCatalogQuery;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ProductCatalogViewRepositoryInterface $productCatalogQuery
    ) {
        $this->viewHandler = $viewHandler;
        $this->productCatalogQuery = $productCatalogQuery;
    }

    public function __invoke(Request $request): Response
    {
        if (!$request->query->has('channel')) {
            throw new NotFoundHttpException('Cannot find product without channel provided');
        }

        try {
            return $this->viewHandler->handle(View::create($this->productCatalogQuery->findByTaxonCode(
                $request->attributes->get('code'),
                $request->query->get('channel'),
                new PaginatorDetails($request->attributes->get('_route'), $request->query->all()),
                $request->query->get('locale')
            ), Response::HTTP_OK));
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
