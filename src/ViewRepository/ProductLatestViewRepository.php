<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ProductListView;
use Webmozart\Assert\Assert;

final class ProductLatestViewRepository implements ProductLatestViewRepositoryInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var ProductRepositoryInterface  */
    private $productRepository;

    /** @var ProductViewFactoryInterface */
    private $productViewFactory;

    /**
     * ProductLatestViewRepository constructor.
     * @param ChannelRepositoryInterface $channelRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ProductViewFactoryInterface $productViewFactory
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductViewFactoryInterface $productViewFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->productRepository = $productRepository;
        $this->productViewFactory = $productViewFactory;
    }

    public function getLatestProducts(string $channelCode, ?string $localeCode, int $count): ProductListView
    {
        $channel = $this->getChannel($channelCode);
        $localeCode = $this->getLocaleCode($localeCode, $channel);
        $latestProducts = $this->productRepository->findLatestByChannel($channel, $localeCode, $count);

        Assert::notNull($latestProducts, sprintf('Latest Products not found in %s locale.', $localeCode));

        $productListView = new ProductListView();

        foreach ($latestProducts as $product) {
            $productListView->items[] = $this->productViewFactory->create($product, $channel, $localeCode);
        }

        return $productListView;
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

    /**
     * @param string $localeCode
     * @param iterable|LocaleInterface[] $supportedLocales
     */
    private function assertLocaleSupport(string $localeCode, iterable $supportedLocales):void
    {
        $supportedLocaleCodes = [];
        foreach ($supportedLocales as $locale) {
            $supportedLocaleCodes[] = $locale->getCode();
        }

        Assert::oneOf($localeCode, $supportedLocaleCodes);
    }
}
