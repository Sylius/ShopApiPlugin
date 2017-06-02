<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Command\AddReview;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class AddReviewSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('pale-ale', 'WEB_GB', 'Awesome beer', 5, 'I love this beer', 'pale.ale@brewery.com');
    }

    public function it_has_product_slug()
    {
        $this->productSlug()->shouldReturn('pale-ale');
    }

    public function it_has_channel_code()
    {
        $this->channelCode()->shouldReturn('WEB_GB');
    }

    public function it_has_title()
    {
        $this->title()->shouldReturn('Awesome beer');
    }

    public function it_has_rating()
    {
        $this->rating()->shouldReturn(5);
    }

    public function it_has_comment()
    {
        $this->comment()->shouldReturn('I love this beer');
    }

    public function it_has_email()
    {
        $this->email()->shouldReturn('pale.ale@brewery.com');
    }
}
