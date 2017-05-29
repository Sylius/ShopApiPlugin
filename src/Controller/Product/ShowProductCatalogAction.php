<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\PageLinksView;
use Sylius\ShopApiPlugin\View\PageView;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

final class ShowProductCatalogAction
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var ProductViewFactoryInterface
     */
    private $productViewFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     * @param ProductRepositoryInterface $productRepository
     * @param TaxonRepositoryInterface $taxonRepository
     * @param ViewHandlerInterface $viewHandler
     * @param ProductViewFactoryInterface $productViewFactory
     * @param RouterInterface $router
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        TaxonRepositoryInterface $taxonRepository,
        ViewHandlerInterface $viewHandler,
        ProductViewFactoryInterface $productViewFactory,
        RouterInterface $router
    ) {
        $this->channelRepository = $channelRepository;
        $this->productRepository = $productRepository;
        $this->taxonRepository = $taxonRepository;
        $this->viewHandler = $viewHandler;
        $this->productViewFactory = $productViewFactory;
        $this->router = $router;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        if (!$request->query->has('channel')) {
            throw new NotFoundHttpException('Cannot find product without channel provided');
        }

        $channelCode = $request->query->get('channel');
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        if (null === $channel) {
            throw new NotFoundHttpException(sprintf('Channel with code %s has not been found', $channelCode));
        }

        $locale = $request->query->has('locale') ? $request->query->get('locale') : $channel->getDefaultLocale()->getCode();

        $taxonSlug = $request->attributes->get('taxonomy');
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonRepository->findOneBySlug($taxonSlug, $locale);

        if (null === $taxon) {
            throw new NotFoundHttpException(sprintf('Taxon with slug %s in locale %s has not been found', $taxonSlug, $locale));
        }

        $queryBuilder = $this->productRepository->createShopListQueryBuilder($channel, $taxon, $locale);
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage($request->query->get('limit', 10));
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        $page = new PageView();
        $page->page = $pagerfanta->getCurrentPage();
        $page->limit = $pagerfanta->getMaxPerPage();
        $page->pages = $pagerfanta->getNbPages();
        $page->total = $pagerfanta->getNbResults();

        $page->links = new PageLinksView();

        $page->links->self = $this->router->generate('shop_api_product_show_catalog', [
            'taxonomy' => $taxonSlug,
            'page' => $pagerfanta->getCurrentPage(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);
        $page->links->first = $this->router->generate('shop_api_product_show_catalog', [
            'taxonomy' => $taxonSlug,
            'page' => 1,
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);
        $page->links->last = $this->router->generate('shop_api_product_show_catalog', [
            'taxonomy' => $taxonSlug,
            'page' => $pagerfanta->getNbPages(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);
        $page->links->next = $this->router->generate('shop_api_product_show_catalog', [
            'taxonomy' => $taxonSlug,
            'page' => ($pagerfanta->getCurrentPage() < $pagerfanta->getNbPages()) ? $pagerfanta->getCurrentPage() + 1 : $pagerfanta->getCurrentPage(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);

        foreach ($pagerfanta->getCurrentPageResults() as $currentPageResult) {
            $page->items[] = $this->productViewFactory->create($currentPageResult, $channel, $locale);
        }

        return $this->viewHandler->handle(View::create($page, Response::HTTP_OK));
    }
}
