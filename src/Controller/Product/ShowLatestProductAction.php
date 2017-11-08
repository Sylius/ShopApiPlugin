<?php
declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\ViewRepository\ProductLatestViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowLatestProductAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ProductLatestViewRepositoryInterface */
    private $productLatestQuery;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ProductLatestViewRepositoryInterface $productLatestQuery
    ) {
        $this->viewHandler = $viewHandler;
        $this->productLatestQuery = $productLatestQuery;
    }

    public function __invoke(Request $request): Response
    {
        if (!$request->query->has('channel')) {
            throw new NotFoundHttpException('Cannot find product without channel provided');
        }

        try {
            return $this->viewHandler->handle(View::create($this->productLatestQuery->getLatestProducts(
                $request->query->get('channel'),
                $request->query->get('locale'),
                $request->query->getInt('limit', 4)
            ), Response::HTTP_OK));
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}