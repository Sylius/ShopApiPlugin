<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory;

use Liip\ImagineBundle\Service\FilterService;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ImageView;

final class ImageViewFactorySpec extends ObjectBehavior
{
    function let(FilterService $filterService): void
    {
        $this->beConstructedWith(ImageView::class, $filterService, 'some_filter');
    }

    function it_is_image_view_factory(): void
    {
        $this->shouldHaveType(ImageViewFactoryInterface::class);
    }

    function it_generates_a_cache_path_for_the_images_with_one_filter(
        ImageViewFactoryInterface $baseFactory,
        FilterService $filterService,
        ImageInterface $image
    ): void {
        $image->getType()->willReturn('thumbnail');
        $image->getPath()->willReturn('abc/a.png');

        $imageView = new ImageView();
        $imageView->code = 'thumbnail';
        $imageView->path = 'cache/abc/a.png';

        $filterService->getUrlOfFilteredImage('abc/a.png', 'some_filter')->willReturn('cache/abc/a.png');

        $this->create($image)->shouldBeLike($imageView);
    }
}
