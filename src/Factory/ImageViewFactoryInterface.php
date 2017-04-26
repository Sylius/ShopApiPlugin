<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\View\ImageView;

interface ImageViewFactoryInterface
{
    /**
     * @param ImageInterface $image
     *
     * @return ImageView
     */
    public function create(ImageInterface $image);
}
