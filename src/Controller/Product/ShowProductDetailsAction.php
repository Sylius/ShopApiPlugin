<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowProductDetailsAction
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
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var ProductViewFactoryInterface
     */
    private $productViewFactory;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ViewHandlerInterface $viewHandler
     * @param ProductViewFactoryInterface $productViewFactory
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ViewHandlerInterface $viewHandler,
        ProductViewFactoryInterface $productViewFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->productRepository = $productRepository;
        $this->viewHandler = $viewHandler;
        $this->productViewFactory = $productViewFactory;
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

        /** @var ChannelInterface $channel */
        $channelCode = $request->query->get('channel');
        $channel = $this->channelRepository->findOneByCode($channelCode);

        if (null === $channel) {
            throw new NotFoundHttpException(sprintf('Channel with code %s has not been found', $channelCode));
        }

        $locale = $request->query->has('locale') ? $request->query->get('locale') : $channel->getDefaultLocale()->getCode();

        $productSlug = $request->attributes->get('slug');
        $product = $this->productRepository->findOneByChannelAndSlug($channel, $locale, $productSlug);

        if (null === $product) {
            throw new NotFoundHttpException(sprintf('Product with slug %s has not been found in %s locale.', $productSlug, $locale));
        }

        return $this->viewHandler->handle(View::create($this->productViewFactory->create($product, $channel, $locale), Response::HTTP_OK));
    }
}
