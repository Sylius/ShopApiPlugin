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

namespace Sylius\ShopApiPlugin\ViewRepository\Product;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Sylius\ShopApiPlugin\View\Product\ProductListView;
use Webmozart\Assert\Assert;

final class ProductLatestViewRepository implements ProductLatestViewRepositoryInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductViewFactoryInterface */
    private $productViewFactory;

    /** @var SupportedLocaleProviderInterface */
    private $supportedLocaleProvider;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductViewFactoryInterface $productViewFactory,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ) {
        $this->channelRepository = $channelRepository;
        $this->productRepository = $productRepository;
        $this->productViewFactory = $productViewFactory;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
    }

    public function getLatestProducts(string $channelCode, ?string $localeCode, int $count): ProductListView
    {
        $channel = $this->getChannel($channelCode);
        $localeCode = $this->supportedLocaleProvider->provide($localeCode, $channel);
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
}
