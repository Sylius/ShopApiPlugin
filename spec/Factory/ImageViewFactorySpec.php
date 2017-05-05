<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ImageView;

final class ImageViewFactorySpec extends ObjectBehavior
{
    function let(CacheManager $imagineCacheManager)
    {
        $this->beConstructedWith($imagineCacheManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImageViewFactory::class);
    }

    function it_is_image_view_factory()
    {
        $this->shouldHaveType(ImageViewFactoryInterface::class);
    }

    function it_creates_image_view(CacheManager $imagineCacheManager, ImageInterface $image)
    {
        $image->getType()->willReturn('thumbnail');
        $image->getPath()->willReturn('/ou/some.jpg');

        $imagineCacheManager->getBrowserPath('/ou/some.jpg', 'sylius_small')->willReturn('http://localhost/media/cache/ou/some.jpg');

        $imageView = new ImageView();
        $imageView->code = 'thumbnail';
        $imageView->url = 'http://localhost/media/cache/ou/some.jpg';

        $this->create($image)->shouldBeLike($imageView);
    }
}
