<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ImageView;

final class ImageViewFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(ImageView::class);
    }

    function it_is_image_view_factory()
    {
        $this->shouldHaveType(ImageViewFactoryInterface::class);
    }

    function it_creates_image_view(ImageInterface $image)
    {
        $image->getType()->willReturn('thumbnail');
        $image->getPath()->willReturn('/ou/some.jpg');

        $imageView = new ImageView();
        $imageView->code = 'thumbnail';
        $imageView->path = '/ou/some.jpg';

        $this->create($image)->shouldBeLike($imageView);
    }
}
