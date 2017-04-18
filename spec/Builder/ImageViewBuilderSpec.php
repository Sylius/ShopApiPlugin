<?php

namespace spec\Sylius\ShopApiPlugin\Builder;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\Builder\ImageViewBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\Builder\ImageViewBuilderInterface;
use Sylius\ShopApiPlugin\View\ImageView;

final class ImageViewBuilderSpec extends ObjectBehavior
{
    function let(CacheManager $imagineCacheManager)
    {
        $this->beConstructedWith($imagineCacheManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImageViewBuilder::class);
    }

    function it_is_image_view_builder()
    {
        $this->shouldHaveType(ImageViewBuilderInterface::class);
    }

    function it_builds_image_view(CacheManager $imagineCacheManager, ImageInterface $image)
    {
        $image->getType()->willReturn('thumbnail');
        $image->getPath()->willReturn('/ou/some.jpg');

        $imagineCacheManager->getBrowserPath('/ou/some.jpg', 'sylius_small')->willReturn('http://localhost/media/cache/ou/some.jpg');

        $imageView = new ImageView();
        $imageView->code = 'thumbnail';
        $imageView->url = 'http://localhost/media/cache/ou/some.jpg';

        $this->build($image)->shouldBeLike($imageView);
    }
}
