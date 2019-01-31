<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Taxon;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\View\ImageView;

interface ImageViewFactoryInterface
{
    public function create(ImageInterface $image): ImageView;
}
