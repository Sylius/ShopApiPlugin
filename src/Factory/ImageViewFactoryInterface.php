<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\View\ImageView;

interface ImageViewFactoryInterface
{
    public function create(ImageInterface $image): ImageView;
}
