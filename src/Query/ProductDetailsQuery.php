<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Query;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\Locale;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ProductView;
use Webmozart\Assert\Assert;

final class ProductDetailsQuery implements ProductDetailsQueryInterface
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
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        Assert::notNull($channel, sprintf('Channel with code %s has not been found', $channelCode));

        $localeCode = $localeCode ?? $channel->getDefaultLocale()->getCode();
        $this->assertLocaleSupport($localeCode, $channel->getLocales());

        $product = $this->productRepository->findOneByChannelAndSlug($channel, $localeCode, $productSlug);

        Assert::notNull($product, sprintf('Product with slug %s has not been found in %s locale.', $productSlug, $localeCode));

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
}
