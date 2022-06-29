<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Product;

use PhpSpec\ObjectBehavior;

final class AddProductReviewByCodeSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('PALE_ALE_CODE', 'WEB_GB', 'Awesome beer', 5, 'I love this beer', 'pale.ale@brewery.com');
    }

    public function it_has_product_slug(): void
    {
        $this->productCode()->shouldReturn('PALE_ALE_CODE');
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
