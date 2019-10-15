<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Product;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Sylius\ShopApiPlugin\View\Product\ProductListView;
use Webmozart\Assert\Assert;
use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\View\Product\PageView;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Sylius\ShopApiPlugin\Factory\Product\PageViewFactory;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository;

final class ProductSearchViewRepository
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
        $this->channelRepository       = $channelRepository;
        $this->productRepository       = $productRepository;
        $this->productViewFactory      = $productViewFactory;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
    }

    public function searchProducts(
        string $channelCode,
        ?string $localeCode,
        string $string
    ) {
        $channel             = $this->getChannel($channelCode);
        $localeCode          = $this->supportedLocaleProvider->provide($localeCode, $channel);
        $foundProducts =
            $this->productRepository->findProductsByString($channel, $localeCode, $string)->getQuery()->getResult();

        Assert::notNull($foundProducts,
            sprintf('Products bu given string not found in %s locale.', $localeCode)
        );
        $foundProductsArray = [];
        foreach ($foundProducts as $product){
            $foundProductsArray[] = $this->productViewFactory->create($product, $channel, $localeCode);
        }
        return $foundProductsArray;
    }

    private function getChannel(string $channelCode): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        Assert::notNull($channel, sprintf('Channel with code %s has not been found.', $channelCode));

        return $channel;
    }

}
