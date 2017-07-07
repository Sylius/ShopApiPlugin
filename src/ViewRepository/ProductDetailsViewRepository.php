<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\Locale;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ProductView;
use Webmozart\Assert\Assert;

final class ProductDetailsViewRepository implements ProductDetailsViewRepositoryInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductViewFactoryInterface */
    private $productViewFactory;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductViewFactoryInterface $productViewFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->productRepository = $productRepository;
        $this->productViewFactory = $productViewFactory;
    }

    public function findOneBySlug(string $channelCode, string $productSlug, ?string $localeCode): ProductView
    {
        $channel = $this->getChannel($channelCode);
        $localeCode = $this->getLocaleCode($localeCode, $channel);

        $product = $this->productRepository->findOneByChannelAndSlug($channel, $localeCode, $productSlug);

        Assert::notNull($product, sprintf('Product with slug %s has not been found in %s locale.', $productSlug, $localeCode));

        return $this->productViewFactory->create($product, $channel, $localeCode);
    }

    public function findOneByCode(string $channelCode, string $productCode, ?string $localeCode): ProductView
    {
        $channel = $this->getChannel($channelCode);
        $localeCode = $this->getLocaleCode($localeCode, $channel);

        $product = $this->productRepository->findOneByCode($productCode);

        Assert::notNull($product, sprintf('Product with code %s has not been found.', $productCode));
        Assert::true($product->hasChannel($channel), sprintf('Channel with code %s has not been found.', $channelCode));

        return $this->productViewFactory->create($product, $channel, $localeCode);
    }

    /**
     * @param string $localeCode
     * @param iterable|Locale[] $supportedLocales
     */
    private function assertLocaleSupport(string $localeCode, iterable $supportedLocales)
    {
        $supportedLocaleCodes = [];
        foreach ($supportedLocales as $locale) {
            $supportedLocaleCodes[] = $locale->getCode();
        }

        Assert::oneOf($localeCode, $supportedLocaleCodes);
    }

    private function getChannel(string $channelCode): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        Assert::notNull($channel, sprintf('Channel with code %s has not been found.', $channelCode));

        return $channel;
    }

    private function getLocaleCode(?string $localeCode, ChannelInterface $channel): string
    {
        $localeCode = $localeCode ?? $channel->getDefaultLocale()->getCode();
        $this->assertLocaleSupport($localeCode, $channel->getLocales());

        return $localeCode;
    }
}
