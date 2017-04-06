<?php

namespace Sylius\ShopApiPlugin\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\View\ImageView;
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
    public function showAction(Request $request)
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
        /** @var CacheManager $imagineCacheManager */
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        /** @var ChannelInterface $channel */
        $channel = $channelRepository->findOneByCode($request->query->get('channel'));
        $locale = $request->query->has('locale') ? $request->query->get('locale') : $channel->getDefaultLocale()->getCode();

        $product = $productRepository->findOneByChannelAndSlug($channel, $locale, $request->attributes->get('slug'));
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $product->getVariants()[0];

        $productView = new ProductView();
        $productView->name = $product->getName();
        $productView->code = $product->getCode();
        $productView->slug = $product->getSlug();
        $productView->price = $productVariant->getChannelPricingForChannel($channel)->getPrice();

        foreach ($product->getImages() as $image) {
            $imageView = new ImageView();
            $imageView->code = $image->getType();
            $imageView->url = $imagineCacheManager->getBrowserPath($image->getPath(), 'sylius_small');

            $productView->images[] = $imageView;
        }

        return $viewHandler->handle(View::create($productView, Response::HTTP_OK));
    }
}
