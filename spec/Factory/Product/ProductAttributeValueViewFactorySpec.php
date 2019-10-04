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
        $productAttributeValue->getType()->willReturn('text');
        $productAttributeValue->getAttribute()->willReturn($productAttribute);

        $productAttribute->getTranslation('en_GB')->willReturn($productAttributeTranslation);

        $productAttributeTranslation->getName()->willReturn('Certificate XPTO');

        $productAttributeValueView = new ProductAttributeValueView();
        $productAttributeValueView->code = 'CERTIFICATE_1';
        $productAttributeValueView->name = 'Certificate XPTO';
        $productAttributeValueView->value = 'Nice, shinny certificate.';

        $this->create($productAttributeValue, 'en_GB')->shouldBeLike($productAttributeValueView);
    }

    function it_creates_product_attribute_value_view_for_select_attribute_type(
        ProductAttributeValueInterface $productAttributeValue,
        ProductAttributeInterface $productAttribute,
        ProductAttributeTranslationInterface $productAttributeTranslation
    ) {
        $productAttributeValue->getCode()->willReturn('CERTIFICATE_1');
        $productAttributeValue->getValue()->willReturn(['1']);
        $productAttributeValue->getType()->willReturn('select');
        $productAttributeValue->getAttribute()->willReturn($productAttribute);

        $productAttribute->getTranslation('en_GB')->willReturn($productAttributeTranslation);
        $productAttribute->getConfiguration()->willReturn([
            'choices' => [
                '1' => [
                    'en_GB' => 'Nice, shinny certificate.',
                ],
            ],
        ]);

        $productAttributeTranslation->getName()->willReturn('Certificate XPTO');

        $productAttributeValueView = new ProductAttributeValueView();
        $productAttributeValueView->code = 'CERTIFICATE_1';
        $productAttributeValueView->name = 'Certificate XPTO';
        $productAttributeValueView->value = ['Nice, shinny certificate.'];

        $this->create($productAttributeValue, 'en_GB')->shouldBeLike($productAttributeValueView);
    }

    function it_creates_product_attribute_value_view_for_select_attribute_type_for_multiple_choices(
        ProductAttributeValueInterface $productAttributeValue,
        ProductAttributeInterface $productAttribute,
        ProductAttributeTranslationInterface $productAttributeTranslation
    ) {
        $productAttributeValue->getCode()->willReturn('CERTIFICATES');
        $productAttributeValue->getValue()->willReturn(['1', '2']);
        $productAttributeValue->getType()->willReturn('select');
        $productAttributeValue->getAttribute()->willReturn($productAttribute);

        $productAttribute->getTranslation('en_GB')->willReturn($productAttributeTranslation);
        $productAttribute->getConfiguration()->willReturn([
            'choices' => [
                '1' => [
                    'en_GB' => 'Certified Software Developer Program.',
                ],
                '2' => [
                    'en_GB' => 'Certified Software Tester.',
                ],
            ],
        ]);

        $productAttributeTranslation->getName()->willReturn('IT Certificats');

        $productAttributeValueView = new ProductAttributeValueView();
        $productAttributeValueView->code = 'CERTIFICATES';
        $productAttributeValueView->name = 'IT Certificats';
        $productAttributeValueView->value = [
            'Certified Software Developer Program.',
            'Certified Software Tester.',
        ];

        $this->create($productAttributeValue, 'en_GB')->shouldBeLike($productAttributeValueView);
    }
}
