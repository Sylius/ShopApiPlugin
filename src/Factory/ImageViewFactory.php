<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Liip\ImagineBundle\Service\FilterService;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\Transformer\Transformer;
use Sylius\ShopApiPlugin\View\ImageView;

final class ImageViewFactory implements ImageViewFactoryInterface
{

    use Transformer;

    public $defaultIncludes = [
        'code',
        'alt',
        'title',
        'path',
        'cachedPath',
    ];


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
        string $viewClass,
        ?string $cloudUrl = null,
        bool $disableLiipImage = false
    ) {
        $this->imageViewClass   = $imageViewClass;
        $this->filterService    = $filterService;
        $this->filter           = $filter;
        $this->viewClass        = $viewClass;
        $this->cloudUrl         = $cloudUrl;
        $this->disableLiipImage = $disableLiipImage;
    }

    public function create(ImageInterface $image): ImageView
    {
        /** @var ImageView $imageView */
        $imageView = $this->generate($image);

        return $imageView;
    }

    public function getCode(ImageInterface $image, $imageView)
    {
        $imageView->code = $image->getType();

        return $imageView;
    }

    public function getPath(ImageInterface $image, $imageView)
    {
        $imageView->path = $image->getPath();

        return $imageView;
    }

    public function getAlt(ImageInterface $image, $imageView)
    {
        if(method_exists($image, 'getAlt')){
            $imageView->alt   = $image->getAlt();
        }
        return $imageView;
    }

    public function getTitle(ImageInterface $image, $imageView)
    {
        if(method_exists($image, 'getTitle')){
            $imageView->title = $image->getTitle();
        }

        return $imageView;
    }

    public function getCachedPath(ImageInterface $image, $imageView)
    {
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
