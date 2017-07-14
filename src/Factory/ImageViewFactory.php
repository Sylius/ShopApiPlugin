<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\View\ImageView;

final class ImageViewFactory implements ImageViewFactoryInterface
{
    /** @var string */
    private $imageViewClass;

    public function __construct(string $imageViewClass)
    {
        $this->imageViewClass = $imageViewClass;
    }

    public function create(ImageInterface $image): ImageView
    {
        /** @var ImageView $imageView */
        $imageView = new $this->imageViewClass();
        $imageView->code = $image->getType();
        $imageView->path = $image->getPath();

        return $imageView;
    }
}
