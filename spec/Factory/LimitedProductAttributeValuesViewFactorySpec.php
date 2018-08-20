<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\SyliusShopApiPlugin\Factory\ProductAttributeValuesViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Factory\ProductAttributeValueViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\View\ProductAttributeValueView;

final class LimitedProductAttributeValuesViewFactorySpec extends ObjectBehavior
{
    function let(ProductAttributeValueViewFactoryInterface $productAttributeValueViewFactory)
    {
        $this->beConstructedWith($productAttributeValueViewFactory, ['CERTIFICATE_ATTRIBUTE']);
    }

    function it_is_product_attribute_values_view_facotry()
    {
        $this->shouldHaveType(ProductAttributeValuesViewFactoryInterface::class);
    }

    function it_creates_filitered_array_of_product_attribute_values(
        ProductAttributeValueInterface $skippedValue,
        ProductAttributeValueInterface $serializedValue,
        ProductAttributeValueViewFactoryInterface $productAttributeValueViewFactory
    ) {
        $productAttributeValueViewFactory->create($skippedValue, 'en_GB')->shouldNotBeCalled();
        $productAttributeValueViewFactory->create($serializedValue, 'en_GB')->willReturn(new ProductAttributeValueView());

        $serializedValue->getCode()->willReturn('CERTIFICATE_ATTRIBUTE');
        $skippedValue->getCode()->willReturn('THIS_CODE_SHOULD_NOT_BE_PARSED');

        $this->create([$skippedValue, $serializedValue], 'en_GB')->shouldBeLike([new ProductAttributeValueView()]);
    }
}
