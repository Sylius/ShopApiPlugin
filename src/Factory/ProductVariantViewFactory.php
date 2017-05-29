<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\ShopApiPlugin\View\ProductVariantView;

final class ProductVariantViewFactory implements ProductVariantViewFactoryInterface
{
    /**
     * @var PriceViewFactoryInterface
     */
    private $priceViewFactory;

    /**
     * @param PriceViewFactoryInterface $priceViewFactory
     */
    public function __construct(PriceViewFactoryInterface $priceViewFactory)
    {
        $this->priceViewFactory = $priceViewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ProductVariantInterface $variant, ChannelInterface $channel, string $locale): \Sylius\ShopApiPlugin\View\ProductVariantView
    {
        $variantView = new ProductVariantView();

        $variantView->code = $variant->getCode();
        $variantView->name = $variant->getTranslation($locale)->getName();
        $variantView->price = $this->priceViewFactory->create($variant->getChannelPricingForChannel($channel)->getPrice());

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
