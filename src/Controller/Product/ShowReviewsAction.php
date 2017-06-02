<?php

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\PageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductReviewViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\PageViewRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @var PageViewFactoryInterface
     */
    private $pageViewFactory;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductReviewRepositoryInterface $productReviewRepository,
        ViewHandlerInterface $viewHandler,
        ProductReviewViewFactoryInterface $productReviewViewFactory,
        PageViewFactoryInterface $pageViewFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->productReviewRepository = $productReviewRepository;
        $this->viewHandler = $viewHandler;
        $this->productReviewViewFactory = $productReviewViewFactory;
        $this->pageViewFactory = $pageViewFactory;
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

        $locale = $request->query->get('locale') ?: $channel->getDefaultLocale()->getCode();

        $reviews = $this->productReviewRepository->findAcceptedByProductSlugAndChannel($request->attributes->get('slug'), $locale, $channel);

        $pagerfanta = new Pagerfanta(new ArrayAdapter($reviews));

        $pagerfanta->setMaxPerPage($request->query->get('limit', 10));
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        $page = $this->pageViewFactory->create($pagerfanta, $request->attributes->get('_route'), array_merge(
            $request->query->all(),
            ['slug' => $request->attributes->get('slug')]
        ));

        foreach ($pagerfanta->getCurrentPageResults() as $currentPageResult) {
            $page->items[] = $this->productReviewViewFactory->create($currentPageResult);
        }

        return $this->viewHandler->handle(View::create($page, Response::HTTP_OK));
    }
}
