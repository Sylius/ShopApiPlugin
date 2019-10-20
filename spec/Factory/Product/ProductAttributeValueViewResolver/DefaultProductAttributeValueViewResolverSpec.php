<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewResolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewResolver\ProductAttributeValueViewResolverInterface;

final class DefaultProductAttributeValueViewResolverSpec extends ObjectBehavior
{
    function it_is_a_product_attribute_value_view_resolver(): void
    {
        $this->shouldHaveType(ProductAttributeValueViewResolverInterface::class);
    }

    function it_returns_a_value(ProductAttributeValueInterface $productAttributeValue): void
    {
        $productAttributeValue->getValue()->willReturn('Wood');

        $this->getValue($productAttributeValue, 'en_GB')->shouldBeLike('Wood');
    }
}
