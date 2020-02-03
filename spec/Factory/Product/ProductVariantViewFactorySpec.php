<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Product;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionTranslationInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductOptionValueTranslationInterface;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Sylius\ShopApiPlugin\Exception\ViewCreationException;
use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\View\PriceView;
use Sylius\ShopApiPlugin\View\Product\ProductVariantView;

final class ProductVariantViewFactorySpec extends ObjectBehavior
{
    function let(PriceViewFactoryInterface $priceViewFactory, AvailabilityCheckerInterface $availabilityChecker): void
    {
        $this->beConstructedWith($priceViewFactory, $availabilityChecker, ProductVariantView::class);
    }

    function it_is_price_view_factory(): void
    {
        $this->shouldHaveType(ProductVariantViewFactoryInterface::class);
    }

    function it_builds_product_variant_view(
        PriceViewFactoryInterface $priceViewFactory,
        AvailabilityCheckerInterface $availabilityChecker,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        ChannelPricingInterface $channelPrice,
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
    ): void {
        $variantView = new ProductVariantView();

        $variant->getCode()->willReturn('SMALL_RED_LOGAN_HAT_CODE');
        $variant->getTranslation('en_GB')->willReturn($productVariantTranslation);
        $variant->getChannelPricingForChannel($channel)->willReturn($channelPrice);
        $variant->getOptionValues()->willReturn(new ArrayCollection([
            $firstOptionValue->getWrappedObject(),
            $secondOptionValue->getWrappedObject(),
        ]));

        $priceViewFactory->create(500, 'PLN')->willReturn(new PriceView());
        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

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

        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('PLN');
        $channelPrice->getPrice()->willReturn(500);
        $channelPrice->getOriginalPrice()->willReturn(null);

        $variantView->code = 'SMALL_RED_LOGAN_HAT_CODE';
        $variantView->name = 'Small red Logan hat code';
        $variantView->price = new PriceView();
        $variantView->axis = ['HAT_SIZE_S', 'HAT_COLOR_RED'];
        $variantView->available = true;
        $variantView->nameAxis = [
            'HAT_SIZE_S' => 'Size S',
            'HAT_COLOR_RED' => 'Color Red',
        ];

        $this->create($variant, $channel, 'en_GB')->shouldBeLike($variantView);
    }

    function it_builds_product_variant_view_with_original_price(
        PriceViewFactoryInterface $priceViewFactory,
        AvailabilityCheckerInterface $availabilityChecker,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        ChannelPricingInterface $channelPrice,
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
    ): void {
        $variantView = new ProductVariantView();

        $availabilityChecker->isStockAvailable($variant)->willReturn(false);

        $variant->getCode()->willReturn('SMALL_RED_LOGAN_HAT_CODE');
        $variant->getOnHand()->willReturn(0);
        $variant->getTranslation('en_GB')->willReturn($productVariantTranslation);
        $variant->getChannelPricingForChannel($channel)->willReturn($channelPrice);
        $variant->getOptionValues()->willReturn(new ArrayCollection([
            $firstOptionValue->getWrappedObject(),
            $secondOptionValue->getWrappedObject(),
        ]));

        $priceViewFactory->create(500, 'PLN')->willReturn(new PriceView());
        $priceViewFactory->create(999, 'PLN')->willReturn(new PriceView());

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

        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('PLN');
        $channelPrice->getPrice()->willReturn(500);
        $channelPrice->getOriginalPrice()->willReturn(999);

        $variantView->code = 'SMALL_RED_LOGAN_HAT_CODE';
        $variantView->name = 'Small red Logan hat code';
        $variantView->price = new PriceView();
        $variantView->originalPrice = new PriceView();
        $variantView->axis = ['HAT_SIZE_S', 'HAT_COLOR_RED'];
        $variantView->available = false;
        $variantView->nameAxis = [
            'HAT_SIZE_S' => 'Size S',
            'HAT_COLOR_RED' => 'Color Red',
        ];

        $this->create($variant, $channel, 'en_GB')->shouldBeLike($variantView);
    }

    function it_throws_an_exception_if_there_is_no_price(
        ChannelInterface $channel,
        CurrencyInterface $currency,
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
    ): void {
        $variant->getCode()->willReturn('SMALL_RED_LOGAN_HAT_CODE');
        $variant->getTranslation('en_GB')->willReturn($productVariantTranslation);
        $variant->getChannelPricingForChannel($channel)->willReturn(null);
        $variant->getOptionValues()->willReturn(new ArrayCollection([
            $firstOptionValue->getWrappedObject(),
            $secondOptionValue->getWrappedObject(),
        ]));

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

        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('PLN');

        $this->shouldThrow(ViewCreationException::class)->during('create', [$variant, $channel, 'en_GB']);
    }
}
