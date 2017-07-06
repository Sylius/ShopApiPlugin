<?php

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\Query\ProductCatalogQuery;
use Sylius\ShopApiPlugin\Query\ProductCatalogQueryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowProductCatalogBySlugAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ProductCatalogQueryInterface */
    private $productCatalogQuery;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ProductCatalogQueryInterface $productCatalogQuery
    ) {
        $this->viewHandler = $viewHandler;
        $this->productCatalogQuery = $productCatalogQuery;
    }

    public function __invoke(Request $request): Response
    {
        if (!$request->query->has('channel')) {
            throw new NotFoundHttpException('Cannot find product without channel provided');
        }

        $page = $this->productCatalogQuery->findByTaxonSlug(
            $request->attributes->get('taxonomySlug'),
            $request->query->get('locale'),
            $request->query->get('channel'),
            new PaginatorDetails($request->attributes->get('_route'), $request->query->all())
        );

        return $this->viewHandler->handle(View::create($page, Response::HTTP_OK));
    }
}
