<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionTranslationInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductOptionValueTranslationInterface;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductVariantViewFactory;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Factory\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\View\PriceView;
use Sylius\ShopApiPlugin\View\ProductVariantView;

final class ProductVariantViewFactorySpec extends ObjectBehavior
{
    function let(PriceViewFactoryInterface $priceViewFactory)
    {
        $this->beConstructedWith($priceViewFactory);
    }

    function it_is_price_view_factory()
    {
        $this->shouldHaveType(ProductVariantViewFactoryInterface::class);
    }

    function it_builds_product_variant_view(
        ChannelInterface $channel,
        ChannelPricingInterface $channelPrice,
        PriceViewFactoryInterface $priceViewFactory,
        ProductOptionInterface $firstOption,
        ProductOptionInterface $secondOption,
        ProductOptionTranslationInterface $firstOptionTranslation,
        ProductOptionTranslationInterface $secondOptionTranslation,
        ProductOptionValueInterface $firstOptionValue,
        ProductOptionValueInterface $secondOptionValue,
        ProductOptionValueTranslationInterface $firstOptionValueTranslation,
        ProductOptionValueTranslationInterface $secondOptionValueTranslation,
        ProductVariantInterface $variant,
        ProductVariantTranslationInterface $productVariantTranslation
    ) {
        $variantView = new ProductVariantView();

        $variant->getCode()->willReturn('SMALL_RED_LOGAN_HAT_CODE');
        $variant->getTranslation('en_GB')->willReturn($productVariantTranslation);
        $variant->getChannelPricingForChannel($channel)->willReturn($channelPrice);
        $variant->getOptionValues()->willReturn([$firstOptionValue, $secondOptionValue]);

        $priceViewFactory->create(500)->willReturn(new PriceView());

        $firstOptionValue->getCode()->willReturn('HAT_SIZE_S');
        $firstOptionValue->getTranslation('en_GB')->willReturn($firstOptionValueTranslation);
        $firstOptionValue->getOption()->willReturn($firstOption);
        $firstOption->getTranslation('en_GB')->willReturn($firstOptionTranslation);
        $firstOptionTranslation->getName()->willReturn('Size');
        $firstOptionValueTranslation->getValue()->willReturn('S');

        $secondOptionValue->getCode()->willReturn('HAT_COLOR_RED');
        $secondOptionValue->getTranslation('en_GB')->willReturn($secondOptionValueTranslation);
        $secondOptionValue->getOption()->willReturn($secondOption);
        $secondOption->getTranslation('en_GB')->willReturn($secondOptionTranslation);
        $secondOptionTranslation->getName()->willReturn('Color');
        $secondOptionValueTranslation->getValue()->willReturn('Red');

        $productVariantTranslation->getName()->willReturn('Small red Logan hat code');

        $channelPrice->getPrice()->willReturn(500);

        $variantView->code = 'SMALL_RED_LOGAN_HAT_CODE';
        $variantView->name = 'Small red Logan hat code';
        $variantView->price = new PriceView();
        $variantView->axis = ['HAT_SIZE_S', 'HAT_COLOR_RED'];
        $variantView->nameAxis = [
            'HAT_SIZE_S' => 'Size S',
            'HAT_COLOR_RED' => 'Color Red',
        ];

        $this->create($variant, $channel, 'en_GB')->shouldBeLike($variantView);
    }
}
