<?php

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
use Sylius\ShopApiPlugin\Factory\PageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\PageViewRequest;
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
     * @var PageViewFactoryInterface
     */
    private $pageViewFactory;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        TaxonRepositoryInterface $taxonRepository,
        ViewHandlerInterface $viewHandler,
        ProductViewFactoryInterface $productViewFactory,
        PageViewFactoryInterface $pageViewFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->productRepository = $productRepository;
        $this->taxonRepository = $taxonRepository;
        $this->viewHandler = $viewHandler;
        $this->productViewFactory = $productViewFactory;
        $this->pageViewFactory = $pageViewFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
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

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));

        $pagerfanta->setMaxPerPage($request->query->get('limit', 10));
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        $page = $this->pageViewFactory->create($pagerfanta, $request->attributes->get('_route'), array_merge(
            $request->query->all(),
            ['taxonomy' => $taxonSlug]
        ));

        foreach ($pagerfanta->getCurrentPageResults() as $currentPageResult) {
            $page->items[] = $this->productViewFactory->create($currentPageResult, $channel, $locale);
        }

        return $this->viewHandler->handle(View::create($page, Response::HTTP_OK));
    }
}
