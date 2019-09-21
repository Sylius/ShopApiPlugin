<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Product;

use PhpSpec\ObjectBehavior;

final class AddProductReviewBySlugSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('pale-ale', 'WEB_GB', 'Awesome beer', 5, 'I love this beer', 'pale.ale@brewery.com');
    }

    public function it_has_product_slug(): void
    {
        $this->productSlug()->shouldReturn('pale-ale');
    }

    public function it_has_channel_code(): void
    {
        $this->channelCode()->shouldReturn('WEB_GB');
    }

    public function it_has_title(): void
    {
        $this->title()->shouldReturn('Awesome beer');
    }

    public function it_has_rating(): void
    {
        $this->rating()->shouldReturn(5);
    }

    public function it_has_comment(): void
    {
        $this->comment()->shouldReturn('I love this beer');
    }

    public function it_has_email(): void
    {
        $this->email()->shouldReturn('pale.ale@brewery.com');
    }
}
