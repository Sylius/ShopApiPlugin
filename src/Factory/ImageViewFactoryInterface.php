<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\SyliusShopApiPlugin\View\ImageView;

interface ImageViewFactoryInterface
{
    public function create(ImageInterface $image): ImageView;
}
