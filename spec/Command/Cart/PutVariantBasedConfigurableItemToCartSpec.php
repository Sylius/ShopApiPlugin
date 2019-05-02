<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Cart;

use PhpSpec\ObjectBehavior;

final class PutVariantBasedConfigurableItemToCartSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('ORDERTOKEN', 'T_SHIRT_CODE', 'RED_SMALL_T_SHIRT_CODE', 5);
    }

    function it_has_order_token(): void
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_product_code(): void
    {
        $this->product()->shouldReturn('T_SHIRT_CODE');
    }

    function it_has_product_variant_code(): void
    {
        $this->productVariant()->shouldReturn('RED_SMALL_T_SHIRT_CODE');
    }

    function it_has_quantity(): void
    {
        $this->quantity()->shouldReturn(5);
    }

    function it_throws_an_exception_if_quantity_is_not_less_then_0(): void
    {
        $this->beConstructedWith('ORDERTOKEN', 'T_SHIRT_CODE', 'RED_SMALL_T_SHIRT_CODE', 0);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
