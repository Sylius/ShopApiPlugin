<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\View\PriceView;

final class PriceViewFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(PriceView::class);
    }

    function it_is_price_view_factory(): void
    {
        $this->shouldHaveType(PriceViewFactoryInterface::class);
    }

    function it_builds_price_view(): void
    {
        $priceView = new PriceView();
        $priceView->current = 500;
        $priceView->currency = 'BTC';

        $this->create(500, 'BTC')->shouldBeLike($priceView);
    }
}
