<?php

namespace Sylius\ShopApiPlugin\Factory;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\View\ImageView;

final class ImageViewFactory implements ImageViewFactoryInterface
{
    /**
     * @param ImageInterface $image
     *
     * @return ImageView
     */
    public function create(ImageInterface $image): \Sylius\ShopApiPlugin\View\ImageView
    {
        $imageView = new ImageView();
        $imageView->code = $image->getType();
        $imageView->path = $image->getPath();

        return $imageView;
    }
}
