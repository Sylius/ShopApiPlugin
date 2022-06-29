<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        string $filter,
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

        if ($this->canImageBeFiltered($image->getPath())) {
            $imageView->cachedPath = $this->filterService->getUrlOfFilteredImage($image->getPath(), $this->filter);
        } else {
            $imageView->cachedPath = $image->getPath();
        }

        return $imageView;
    }

    private function canImageBeFiltered(string $path): bool
    {
        return substr($path, -4) !== '.svg';
    }
}
