<?php

namespace Sylius\ShopApiPlugin\Builder;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\View\ImageView;

interface ImageViewBuilderInterface
{
    /**
     * @param ImageInterface $image
     *
     * @return ImageView
     */
    public function build(ImageInterface $image);
}
