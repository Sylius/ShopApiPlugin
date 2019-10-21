<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Product;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeTranslationInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewResolver\ProductAttributeValueViewResolverInterface;
use Sylius\ShopApiPlugin\View\Product\ProductAttributeValueView;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ProductAttributeValueViewFactorySpec extends ObjectBehavior
{
    function let(ServiceLocator $productAttributeValueViewResolversLocator): void
    {
        $this->beConstructedWith(ProductAttributeValueView::class, $productAttributeValueViewResolversLocator);
    }

    function it_is_a_product_attribute_view_factory(): void
    {
        $this->shouldImplement(ProductAttributeValueViewFactoryInterface::class);
    }

    function it_creates_a_product_attribute_value_view(
        ServiceLocator $productAttributeValueViewResolversLocator,
        ProductAttributeValueInterface $productAttributeValue,
        ProductAttributeInterface $productAttribute,
        ProductAttributeTranslationInterface $productAttributeTranslation,
        ProductAttributeValueViewResolverInterface $valueResolver
    ): void {
        $productAttributeValue->getCode()->willReturn('CERTIFICATE_1');
        $productAttributeValue->getValue()->willReturn('Nice, shinny certificate.');
        $productAttributeValue->getType()->willReturn('text');
        $productAttributeValue->getAttribute()->willReturn($productAttribute);

        $productAttribute->getTranslation('en_GB')->willReturn($productAttributeTranslation);
        $productAttributeTranslation->getName()->willReturn('Certificate XPTO');

        $productAttributeValueViewResolversLocator->get('text')->willReturn($valueResolver);
        $valueResolver->getValue($productAttributeValue, 'en_GB')->willReturn('Nice, shinny certificate.');

        $productAttributeValueView = new ProductAttributeValueView();
        $productAttributeValueView->code = 'CERTIFICATE_1';
        $productAttributeValueView->name = 'Certificate XPTO';
        $productAttributeValueView->type = 'text';
        $productAttributeValueView->value = 'Nice, shinny certificate.';

        $this->create($productAttributeValue, 'en_GB')->shouldBeLike($productAttributeValueView);
    }

    function it_creates_product_attribute_value_view_for_select_attribute_type(
        ServiceLocator $productAttributeValueViewResolversLocator,
        ProductAttributeValueInterface $productAttributeValue,
        ProductAttributeInterface $productAttribute,
        ProductAttributeTranslationInterface $productAttributeTranslation,
        ProductAttributeValueViewResolverInterface $valueResolver
    ) {
        $productAttributeValue->getCode()->willReturn('CERTIFICATES');
        $productAttributeValue->getType()->willReturn('select');
        $productAttributeValue->getAttribute()->willReturn($productAttribute);

        $productAttribute->getTranslation('en_GB')->willReturn($productAttributeTranslation);
        $productAttributeTranslation->getName()->willReturn('IT Certificats');

        $productAttributeValueViewResolversLocator->get('select')->willReturn($valueResolver);
        $valueResolver->getValue($productAttributeValue, 'en_GB')->willReturn([
            'Certified Software Developer Program.',
            'Certified Software Tester.',
        ]);

        $productAttributeValueView = new ProductAttributeValueView();
        $productAttributeValueView->code = 'CERTIFICATES';
        $productAttributeValueView->name = 'IT Certificats';
        $productAttributeValueView->type = 'select';
        $productAttributeValueView->value = [
            'Certified Software Developer Program.',
            'Certified Software Tester.',
        ];

        $this->create($productAttributeValue, 'en_GB')->shouldBeLike($productAttributeValueView);
    }
}
