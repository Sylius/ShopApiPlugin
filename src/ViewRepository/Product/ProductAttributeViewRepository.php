<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Product;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\PageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeViewFactory;
use Sylius\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Sylius\ShopApiPlugin\View\Product\PageView;
use Webmozart\Assert\Assert;

final class ProductAttributeViewRepository
{

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;
    /** @var SupportedLocaleProviderInterface */
    private $supportedLocaleProvider;

    /** @var ProductAttributeViewFactory */
    private $attributeViewFactory;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ChannelRepositoryInterface $channelRepository,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ProductAttributeViewFactory $attributeViewFactory
    ) {
        $this->productRepository = $productRepository;
        $this->channelRepository       = $channelRepository;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
        $this->attributeViewFactory    = $attributeViewFactory;
    }

    public function getAttributes(string $channelCode, ?string $localeCode)
    {
        $channel    = $this->getChannel($channelCode);
        $localeCode = $this->supportedLocaleProvider->provide($localeCode, $channel);
        $attributes =
            $this->productRepository->findProductAttributes()->getQuery()->getResult();
        Assert::notNull($attributes, sprintf('Attributes in locale %s has not been found', $localeCode));

        $attributesViewes = [];
        foreach ($attributes as $attribute) {
            $attributesViewes[] = $this->attributeViewFactory->create($attribute, $localeCode);

        }
        return $attributesViewes;
    }

    private function getChannel(string $channelCode): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        Assert::notNull($channel, sprintf('Channel with code %s has not been found.', $channelCode));

        return $channel;
    }
}
