<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\ShopApiPlugin\Factory\ProductViewFactory;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;

class ProductViewFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductViewFactory::class);
    }

    function it_is_product_view_factory()
    {
        $this->shouldHaveType(ProductViewFactoryInterface::class);
    }
}
