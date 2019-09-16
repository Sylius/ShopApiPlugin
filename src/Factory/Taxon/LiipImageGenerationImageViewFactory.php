<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Taxon;

use Liip\ImagineBundle\Service\FilterService;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\View\Taxon\ImageView;

final class LiipImageGenerationImageViewFactory implements ImageViewFactoryInterface
{
    /** @var ImageViewFactoryInterface */
    private $baseFactory;

    /** @var FilterService */
    private $filterService;

    /** @var string[] */
    private $filters;

    public function __construct(
        ImageViewFactoryInterface $baseFactory,
        FilterService $filterService,
        array $filters
    ) {
        $this->baseFactory = $baseFactory;
        $this->filterService = $filterService;
        $this->filters = $filters;
    }

    public function create(ImageInterface $image): ImageView
    {
        $imageView = $this->baseFactory->create($image);
        $imageView->cachedPaths =
            array_map(function (string $filter) use ($image): string {
                return $this->filterService->getUrlOfFilteredImage($image->getPath(), $filter);
            }, $this->filters);

        return $imageView;
    }
}
