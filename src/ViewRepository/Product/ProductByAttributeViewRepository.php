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

final class ProductByAttributeViewRepository
{

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductViewFactoryInterface */
    private $productViewFactory;

    /** @var SupportedLocaleProviderInterface */
    private $supportedLocaleProvider;

    /** @var PageViewFactory */
    private $pageViewFactory;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductViewFactoryInterface $productViewFactory,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        PageViewFactory $pageViewFactory
    ) {
        $this->channelRepository       = $channelRepository;
        $this->productRepository       = $productRepository;
        $this->productViewFactory      = $productViewFactory;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
        $this->pageViewFactory         = $pageViewFactory;
    }

    public function getProductsByAttributes(
        string $channelCode,
        ?string $localeCode,
        $paginatorDetails,
        string $attribute,
        array $values
    ): PageView {
        $channel    = $this->getChannel($channelCode);
        $localeCode = $this->supportedLocaleProvider->provide($localeCode, $channel);
        $productsByAttribute = $this->productRepository->findProductsByAttributes($channel, $localeCode, $attribute, $values);

        Assert::notNull($productsByAttribute, sprintf('Products bu given attribute not found in %s locale.', $localeCode));

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($productsByAttribute));

        $pagerfanta->setMaxPerPage($paginatorDetails->limit());
        $pagerfanta->setCurrentPage($paginatorDetails->page());
        $pageView =
            $this->pageViewFactory->create($pagerfanta, $paginatorDetails->route(), ['attribute' => $attribute, 'values' => implode(',',$values)]);

        foreach ($pagerfanta->getCurrentPageResults() as $currentPageResult) {
            $pageView->items[] = $this->productViewFactory->create($currentPageResult, $channel, $localeCode);
        }
        return $pageView;
    }

    private function getChannel(string $channelCode): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        Assert::notNull($channel, sprintf('Channel with code %s has not been found.', $channelCode));

        return $channel;
    }

}
