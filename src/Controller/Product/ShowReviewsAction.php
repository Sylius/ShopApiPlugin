<?php

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductReviewViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\PageLinksView;
use Sylius\ShopApiPlugin\View\PageView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

final class ShowReviewsAction
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var ProductReviewRepositoryInterface
     */
    private $productReviewRepository;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var ProductReviewViewFactoryInterface
     */
    private $productReviewViewFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductReviewRepositoryInterface $productReviewRepository,
        ViewHandlerInterface $viewHandler,
        ProductReviewViewFactoryInterface $productReviewViewFactory,
        RouterInterface $router
    ) {
        $this->channelRepository = $channelRepository;
        $this->productReviewRepository = $productReviewRepository;
        $this->viewHandler = $viewHandler;
        $this->productReviewViewFactory = $productReviewViewFactory;
        $this->router = $router;
    }

    public function __invoke(Request $request): Response
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
        $slug = $request->attributes->get('slug');

        $locale = $request->query->has('locale') ? $request->query->get('locale') : $channel->getDefaultLocale()->getCode();

        $reviews = $this->productReviewRepository->findAcceptedByProductSlugAndChannel($request->attributes->get('slug'), $locale, $channel);

        $adapter = new ArrayAdapter($reviews);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage($request->query->get('limit', 10));
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        $page = new PageView();
        $page->page = $pagerfanta->getCurrentPage();
        $page->limit = $pagerfanta->getMaxPerPage();
        $page->pages = $pagerfanta->getNbPages();
        $page->total = $pagerfanta->getNbResults();

        $page->links = new PageLinksView();

        $page->links->self = $this->router->generate('shop_api_product_show_reviews', [
            'slug' => $slug,
            'channel' => $channelCode,
            'page' => $pagerfanta->getCurrentPage(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);
        $page->links->first = $this->router->generate('shop_api_product_show_reviews', [
            'slug' => $slug,
            'channel' => $channelCode,
            'page' => 1,
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);
        $page->links->last = $this->router->generate('shop_api_product_show_reviews', [
            'slug' => $slug,
            'channel' => $channelCode,
            'page' => $pagerfanta->getNbPages(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);
        $page->links->next = $this->router->generate('shop_api_product_show_reviews', [
            'slug' => $slug,
            'channel' => $channelCode,
            'page' => ($pagerfanta->getCurrentPage() < $pagerfanta->getNbPages()) ? $pagerfanta->getCurrentPage() + 1 : $pagerfanta->getCurrentPage(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]);

        foreach ($pagerfanta->getCurrentPageResults() as $currentPageResult) {
            $page->items[] = $this->productReviewViewFactory->create($currentPageResult);
        }

        return $this->viewHandler->handle(View::create($page, Response::HTTP_OK));
    }
}
