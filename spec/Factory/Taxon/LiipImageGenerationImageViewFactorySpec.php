<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Taxon;

use Liip\ImagineBundle\Service\FilterService;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\Factory\Taxon\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Taxon\ImageView;

final class LiipImageGenerationImageViewFactorySpec extends ObjectBehavior
{
    public function let(ImageViewFactoryInterface $baseFactory, FilterService $filterService): void
    {
        $this->beConstructedWith($baseFactory, $filterService, ['some_filter']);
    }

    public function it_generates_a_cache_path_for_the_images_with_one_filter(
        ImageViewFactoryInterface $baseFactory,
        FilterService $filterService,
        ImageInterface $image,
        ImageView $imageView
    ): void {
        $baseFactory->create($image)->willReturn($imageView);
        $image->getPath()->willReturn('abc/a.png');

        $filterService->getUrlOfFilteredImage('abc/a.png', 'some_filter')->willReturn('cache/abc/a.png');

        $resultImageView = $this->create($image);
        $resultImageView->shouldHaveType(ImageView::class);
        $resultImageView->cachedPaths->shouldReturn(['cache/abc/a.png']);
    }

    public function it_generates_a_cache_path_for_the_images_with_mulitple_filters(
        ImageViewFactoryInterface $baseFactory,
        FilterService $filterService,
        ImageInterface $image,
        ImageView $imageView
    ): void {
        $this->beConstructedWith($baseFactory, $filterService, ['some_filter', 'big_filter']);

        $baseFactory->create($image)->willReturn($imageView);
        $image->getPath()->willReturn('abc/a.png');

        $filterService->getUrlOfFilteredImage('abc/a.png', 'some_filter')->willReturn('cache/abc/a.png');
        $filterService->getUrlOfFilteredImage('abc/a.png', 'big_filter')->willReturn('cache/ABC/a.png');

        $resultImageView = $this->create($image);
        $resultImageView->shouldHaveType(ImageView::class);
        $resultImageView->cachedPaths->shouldReturn([
            'cache/abc/a.png',
            'cache/ABC/a.png',
        ]);
    }
}
