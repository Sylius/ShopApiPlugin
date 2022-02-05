<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\ShopApiPlugin\Exception\ViewCreationException;
use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Product\ProductVariantView;

final class ProductVariantViewFactory implements ProductVariantViewFactoryInterface
{
    /** @var PriceViewFactoryInterface */
    private $priceViewFactory;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    /** @var string */
    private $productVariantViewClass;

    public function __construct(PriceViewFactoryInterface $priceViewFactory, AvailabilityCheckerInterface $availabilityChecker, string $productVariantViewClass)
    {
        $this->priceViewFactory = $priceViewFactory;
        $this->availabilityChecker = $availabilityChecker;
        $this->productVariantViewClass = $productVariantViewClass;
    }

    /** {@inheritdoc} */
    public function create(ProductVariantInterface $variant, ChannelInterface $channel, string $locale): ProductVariantView
    {
        /** @var ProductVariantView $variantView */
        $variantView = new $this->productVariantViewClass();

        $channelPricing = $variant->getChannelPricingForChannel($channel);
        if (null === $channelPricing) {
            throw new ViewCreationException('Variant does not have pricing.');
        }

        $this->availabilityChecker->isStockAvailable($variant);

        $variantView->code = $variant->getCode();
        $variantView->name = $variant->getTranslation($locale)->getName();
        $variantView->available = $this->availabilityChecker->isStockAvailable($variant);
        $variantView->price = $this->priceViewFactory->create(
            $channelPricing->getPrice(),
            $channel->getBaseCurrency()->getCode()
        );

        $originalPrice = $channelPricing->getOriginalPrice();
        if (null !== $originalPrice) {
            $variantView->originalPrice = $this->priceViewFactory->create(
                $originalPrice,
                $channel->getBaseCurrency()->getCode()
            );
        }

        foreach ($variant->getOptionValues() as $optionValue) {
            $variantView->axis[] = $optionValue->getCode();
            $variantView->nameAxis[$optionValue->getCode()] = sprintf(
                '%s %s',
                $optionValue->getOption()->getTranslation($locale)->getName(),
                $optionValue->getTranslation($locale)->getValue()
            );
        }

        return $variantView;
    }
}
