<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Liip\ImagineBundle\Service\FilterService;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\View\ImageView;

final class ImageViewFactory implements ImageViewFactoryInterface
{
    /** @var string */
    private $imageViewClass;

    /** @var FilterService */
    private $filterService;

    /** @var string */
    private $filter;

    public function __construct(
        string $imageViewClass,
        FilterService $filterService,
        string $filter
    ) {
        $this->imageViewClass = $imageViewClass;
        $this->filterService = $filterService;
        $this->filter = $filter;
    }

    public function create(ImageInterface $image): ImageView
    {
        /** @var ImageView $imageView */
        $imageView = new $this->imageViewClass();

        $imageView->code = $image->getType();
        $imageView->path = $image->getPath();
        // $imageView->cachedPath = $this->filterService->getUrlOfFilteredImage($image->getPath(), $this->filter);

        // это сделано для того, что бы убрать ненужные походы в s3!
        $awsCdn                = getenv('AWS_S3_CDN');
        $imageView->cachedPath = $awsCdn . 'media/cache/' . $this->filter . '/' . $image->getPath();
        
        return $imageView;
    }
}
