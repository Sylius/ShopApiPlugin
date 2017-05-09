<?php

namespace Sylius\ShopApiPlugin\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImage;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ImageView;
use Sylius\ShopApiPlugin\View\PageLinksView;
use Sylius\ShopApiPlugin\View\PageView;
use Sylius\ShopApiPlugin\View\ProductVariantView;
use Sylius\ShopApiPlugin\View\ProductView;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ProductController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showDetailsAction(Request $request)
    {
        if (!$request->query->has('channel')) {
            throw new NotFoundHttpException('Cannot find product without channel provided');
        }

        /** @var ChannelRepositoryInterface $channelRepository */
        $channelRepository = $this->get('sylius.repository.channel');
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->get('sylius.repository.product');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');
        $channelCode = $request->query->get('channel');
        /** @var ChannelInterface $channel */
        $channel = $channelRepository->findOneByCode($channelCode);

        if (null === $channel) {
            throw new NotFoundHttpException(sprintf('Channel with code %s has not been found', $channelCode));
        }

        $locale = $request->query->has('locale') ? $request->query->get('locale') : $channel->getDefaultLocale()->getCode();

        $productSlug = $request->attributes->get('slug');
        $product = $productRepository->findOneByChannelAndSlug($channel, $locale, $productSlug);

        if (null === $product) {
            throw new NotFoundHttpException(sprintf('Product with slug %s has not been found in %s locale.', $productSlug, $locale));
        }

        return $viewHandler->handle(View::create($this->buildProductView($product, $locale, $channel), Response::HTTP_OK));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showCatalogAction(Request $request)
    {
        if (!$request->query->has('channel')) {
            throw new NotFoundHttpException('Cannot find product without channel provided');
        }

        /** @var ChannelRepositoryInterface $channelRepository */
        $channelRepository = $this->get('sylius.repository.channel');
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->get('sylius.repository.product');
        /** @var TaxonRepositoryInterface $taxonRepository */
        $taxonRepository = $this->get('sylius.repository.taxon');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');

        $channelCode = $request->query->get('channel');
        /** @var ChannelInterface $channel */
        $channel = $channelRepository->findOneByCode($channelCode);

        if (null === $channel) {
            throw new NotFoundHttpException(sprintf('Channel with code %s has not been found', $channelCode));
        }

        $locale = $request->query->has('locale') ? $request->query->get('locale') : $channel->getDefaultLocale()->getCode();

        $taxonSlug = $request->attributes->get('taxonomy');
        /** @var TaxonInterface $taxon */
        $taxon = $taxonRepository->findOneBySlug($taxonSlug, $locale);

        if (null === $taxon) {
            throw new NotFoundHttpException(sprintf('Taxon with slug %s in locale %s has not been found', $taxonSlug, $locale));
        }

        $queryBuilder = $productRepository->createShopListQueryBuilder($channel, $taxon, $locale);
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

        $page->links->self = $this->generateUrl('shop_api_product_show_catalog', [
            'taxonomy' => $taxonSlug,
            'page' => $pagerfanta->getCurrentPage(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);
        $page->links->first = $this->generateUrl('shop_api_product_show_catalog', [
            'taxonomy' => $taxonSlug,
            'page' => 1,
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);
        $page->links->last = $this->generateUrl('shop_api_product_show_catalog', [
            'taxonomy' => $taxonSlug,
            'page' => $pagerfanta->getNbPages(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);
        $page->links->next = $this->generateUrl('shop_api_product_show_catalog', [
            'taxonomy' => $taxonSlug,
            'page' => ($pagerfanta->getCurrentPage() < $pagerfanta->getNbPages()) ? $pagerfanta->getCurrentPage() + 1 : $pagerfanta->getCurrentPage(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);

        foreach ($pagerfanta->getCurrentPageResults() as $currentPageResult) {
            $page->items[] = $this->buildProductView($currentPageResult, $locale, $channel);
        }

        return $viewHandler->handle(View::create($page, Response::HTTP_OK));
    }

    /**
     * @param ProductInterface $product
     * @param string $locale
     * @param ChannelInterface $channel
     *
     * @return ProductView
     */
    private function buildProductView(ProductInterface $product, $locale, ChannelInterface $channel)
    {
        /** @var ImageViewFactoryInterface $imageViewFactory */
        $imageViewFactory = $this->get('sylius.shop_api_plugin.factory.image_view_factory');

        /** @var ProductViewFactoryInterface $productViewFactory */
        $productViewFactory = $this->get('sylius.shop_api_plugin.factory.product_view_factory');

        return $productViewFactory->create($product, $locale, $imageViewFactory, $channel);
    }
}
