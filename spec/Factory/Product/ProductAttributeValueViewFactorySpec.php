<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Product;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeTranslationInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Product\ProductAttributeValueView;

final class ProductAttributeValueViewFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(ProductAttributeValueView::class);
    }

    function it_is_product_attribute_view_factory(): void
    {
        $this->shouldImplement(ProductAttributeValueViewFactoryInterface::class);
    }

    function it_creates_product_attribute_value_view(
        ProductAttributeValueInterface $productAttributeValue,
        ProductAttributeInterface $productAttribute,
        ProductAttributeTranslationInterface $productAttributeTranslation
    ): void {
        $productAttributeValue->getCode()->willReturn('CERTIFICATE_1');
        $productAttributeValue->getValue()->willReturn('Nice, shinny certificate.');
        $productAttributeValue->getAttribute()->willReturn($productAttribute);

        $productAttribute->getTranslation('en_GB')->willReturn($productAttributeTranslation);

        $productAttributeTranslation->getName()->willReturn('Certificate XPTO');

        $productAttributeValueView = new ProductAttributeValueView();
        $productAttributeValueView->code = 'CERTIFICATE_1';
        $productAttributeValueView->name = 'Certificate XPTO';
        $productAttributeValueView->value = 'Nice, shinny certificate.';

        $this->create($productAttributeValue, 'en_GB')->shouldBeLike($productAttributeValueView);
    }
}
