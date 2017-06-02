<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\Factory\ProductAttributeValueViewFactory;
use Sylius\ShopApiPlugin\Factory\ProductAttributeValueViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ProductAttributeValueView;

final class ProductAttributeValueViewFactorySpec extends ObjectBehavior
{

    function it_is_product_attribute_view_factory()
    {
        $this->shouldImplement(ProductAttributeValueViewFactoryInterface::class);
    }

    function it_creates_product_attribute_value_view(ProductAttributeValueInterface $productAttributeValue)
    {
        $productAttributeValue->getCode()->willReturn('CERTIFICATE_1');
        $productAttributeValue->getName()->willReturn('Certificate XPTO');
        $productAttributeValue->getValue()->willReturn('Nice, shinny certificate.');

        $productAttributeValueView = new ProductAttributeValueView();
        $productAttributeValueView->code = 'CERTIFICATE_1';
        $productAttributeValueView->name = 'Certificate XPTO';
        $productAttributeValueView->value = 'Nice, shinny certificate.';

        $this->create($productAttributeValue)->shouldBeLike($productAttributeValueView);
    }
}
