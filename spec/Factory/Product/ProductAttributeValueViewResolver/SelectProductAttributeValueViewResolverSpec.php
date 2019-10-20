<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewResolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewResolver\ProductAttributeValueViewResolverInterface;

final class SelectProductAttributeValueViewResolverSpec extends ObjectBehavior
{
    function it_is_a_product_attribute_value_view_resolver(): void
    {
        $this->shouldHaveType(ProductAttributeValueViewResolverInterface::class);
    }

    function it_returns_a_value(
        ProductAttributeValueInterface $productAttributeValue,
        ProductAttributeInterface $productAttribute
    ): void {
        $productAttributeValue->getValue()->willReturn(['1', '2']);
        $productAttributeValue->getAttribute()->willReturn($productAttribute);
        $productAttribute->getConfiguration()->willReturn(['choices' => [
            '1' => ['en_GB' => 'Wood'],
            '2' => ['en_GB' => 'Cotton'],
        ]]);

        $this->getValue($productAttributeValue, 'en_GB')->shouldBeLike(['Wood', 'Cotton']);
    }
}
