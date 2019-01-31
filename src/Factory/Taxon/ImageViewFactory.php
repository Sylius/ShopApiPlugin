<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Taxon;

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
