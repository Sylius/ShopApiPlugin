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
    private $cloudUrl;
    private $disableLiipImage;

    /**
     * @param string        $imageViewClass
     * @param FilterService $filterService
     * @param string        $filter
     * @param null|string   $cloudUrl
     * @param bool          $disableLiipImage
     */
    public function __construct(
        string $imageViewClass,
        FilterService $filterService,
        string $filter,
        ?string $cloudUrl = null,
        bool $disableLiipImage = false
    ) {
        $this->imageViewClass   = $imageViewClass;
        $this->filterService    = $filterService;
        $this->filter           = $filter;
        $this->cloudUrl         = $cloudUrl;
        $this->disableLiipImage = $disableLiipImage;
    }

    public function create(ImageInterface $image): ImageView
    {
        /** @var ImageView $imageView */
        $imageView = new $this->imageViewClass();

        $imageView->code = $image->getType();
        $imageView->path = $image->getPath();
        
        if ($this->disableLiipImage) {
            if($this->cloudUrl){
                $imageView->cachedPath = $this->cloudUrl . $this->filter . '/' . $image->getPath();
            }
        } else {
            $imageView->cachedPath = $this->filterService->getUrlOfFilteredImage($image->getPath(), $this->filter);
        }

        return $imageView;
    }
}
