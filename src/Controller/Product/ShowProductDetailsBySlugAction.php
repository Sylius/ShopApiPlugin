<?php

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\ViewRepository\ProductDetailsViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowProductDetailsBySlugAction
{
    /** @var ProductDetailsViewRepositoryInterface */
    private $productCatalog;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    public function __construct(
        ProductDetailsViewRepositoryInterface $productCatalog,
        ViewHandlerInterface $viewHandler
    ) {
        $this->productCatalog = $productCatalog;
        $this->viewHandler = $viewHandler;
    }

    public function __invoke(Request $request): Response
    {
        if (!$request->query->has('channel')) {
            throw new NotFoundHttpException('Cannot find product without channel provided');
        }

        try {
            return $this->viewHandler->handle(View::create($this->productCatalog->findOneBySlug(
                $request->query->get('channel'),
                $request->attributes->get('slug'),
                $request->query->get('locale')
            ), Response::HTTP_OK));
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
