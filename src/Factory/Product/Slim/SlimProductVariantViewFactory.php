<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product\Slim;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\ShopApiPlugin\Exception\ViewCreationException;
use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\Transformer\Transformer;
use Sylius\ShopApiPlugin\View\Product\ProductVariantView;

final class SlimProductVariantViewFactory implements ProductVariantViewFactoryInterface
{

    use Transformer;

    public $defaultIncludes = [
        'code',
        'position',
        'price',
        'originalPrice',
        'axis',
        'nameAxis'
    ];

    /** @var PriceViewFactoryInterface */
    private $priceViewFactory;

    /** @var string */
    private $locale;
    private $channel;

    public function __construct(PriceViewFactoryInterface $priceViewFactory, string $productVariantViewClass)
    {
        $this->priceViewFactory = $priceViewFactory;
        $this->viewClass        = $productVariantViewClass;
    }

    /** {@inheritdoc} */
    public function create(
        ProductVariantInterface $variant,
        ChannelInterface $channel,
        string $locale
    ): ProductVariantView {
        $this->locale  = $locale;
        $this->channel = $channel;

        /** @var ProductVariantView $variantView */
        $variantView = $this->generate($variant);

        return $variantView;
    }

    public function getCode(ProductVariantInterface $variant, $view)
    {
        $view->code = $variant->getCode();

        return $view;
    }

    public function getPosition(ProductVariantInterface $variant, $view)
    {
        $view->position = $variant->getPosition();

        return $view;
    }

    public function getPrice(ProductVariantInterface $variant, $view)
    {
        $channelPricing = $variant->getChannelPricingForChannel($this->channel);
        if (null === $channelPricing) {
            throw new ViewCreationException('Variant does not have pricing.');
        }
        $view->price = $this->priceViewFactory->create($channelPricing->getPrice(),
            $this->channel->getBaseCurrency()->getCode()
        );

        return $view;
    }

    public function getOriginalPrice(ProductVariantInterface $variant, $view)
    {
        $channelPricing = $variant->getChannelPricingForChannel($this->channel);
        if (null === $channelPricing) {
            throw new ViewCreationException('Variant does not have pricing.');
        }
        $originalPrice = $channelPricing->getOriginalPrice();
        if (null !== $originalPrice) {
            $view->originalPrice = $this->priceViewFactory->create($originalPrice,
                $this->channel->getBaseCurrency()->getCode()
            );
        }

        return $view;
    }

    public function getAxis(ProductVariantInterface $variant, $view)
    {
        foreach ($variant->getOptionValues() as $optionValue) {
            $view->axis[] = $optionValue->getCode();
        }

        return $view;
    }

    public function getNameAxis(ProductVariantInterface $variant, $view)
    {
        foreach ($variant->getOptionValues() as $optionValue) {
            $view->nameAxis[$optionValue->getCode()] = sprintf('%s %s',
                $optionValue->getOption()->getTranslation($this->locale)->getName(),
                $optionValue->getTranslation($this->locale)->getValue()
            );
        }

        return $view;
    }
}
