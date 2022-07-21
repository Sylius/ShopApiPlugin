<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Product;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValuesViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Product\ProductAttributeValueView;

final class LimitedProductAttributeValuesViewFactorySpec extends ObjectBehavior
{
    function let(ProductAttributeValueViewFactoryInterface $productAttributeValueViewFactory): void
    {
        $this->beConstructedWith($productAttributeValueViewFactory, ['CERTIFICATE_ATTRIBUTE']);
    }

    function it_is_product_attribute_values_view_facotry(): void
    {
        $this->shouldHaveType(ProductAttributeValuesViewFactoryInterface::class);
    }

    function it_creates_filtered_array_of_product_attribute_values(
        ProductAttributeValueInterface $skippedValue,
        ProductAttributeValueInterface $serializedValue,
        ProductAttributeValueViewFactoryInterface $productAttributeValueViewFactory,
    ): void {
        $productAttributeValueViewFactory->create($skippedValue, 'en_GB')->shouldNotBeCalled();
        $productAttributeValueViewFactory->create($serializedValue, 'en_GB')->willReturn(new ProductAttributeValueView());

        $serializedValue->getCode()->willReturn('CERTIFICATE_ATTRIBUTE');
        $skippedValue->getCode()->willReturn('THIS_CODE_SHOULD_NOT_BE_PARSED');

        $this->create([$skippedValue, $serializedValue], 'en_GB')->shouldBeLike([new ProductAttributeValueView()]);
    }
}
