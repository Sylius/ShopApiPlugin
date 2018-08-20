<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\ViewRepository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\SyliusShopApiPlugin\Factory\PageViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Model\PaginatorDetails;
use Sylius\SyliusShopApiPlugin\View\PageView;
use Webmozart\Assert\Assert;

final class ProductCatalogViewRepository implements ProductCatalogViewRepositoryInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductViewFactoryInterface */
    private $productViewFactory;

    /** @var TaxonRepositoryInterface */
    private $taxonRepository;

    /** @var PageViewFactoryInterface */
    private $pageViewFactory;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        TaxonRepositoryInterface $taxonRepository,
        ProductViewFactoryInterface $productViewFactory,
        PageViewFactoryInterface $pageViewFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->productRepository = $productRepository;
        $this->productViewFactory = $productViewFactory;
        $this->taxonRepository = $taxonRepository;
        $this->pageViewFactory = $pageViewFactory;
    }

    public function findByTaxonSlug(string $taxonSlug, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView
    {
        $channel = $this->getChannel($channelCode);
        $localeCode = $this->getLocaleCode($localeCode, $channel);

        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonRepository->findOneBySlug($taxonSlug, $localeCode);

        Assert::notNull($taxon, sprintf('Taxon with slug %s in locale %s has not been found', $taxonSlug, $localeCode));
        $paginatorDetails->addToParameters('taxonSlug', $taxonSlug);

        return $this->findByTaxon($taxon, $channel, $paginatorDetails, $localeCode);
    }

    public function findByTaxonCode(string $taxonCode, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView
    {
        $channel = $this->getChannel($channelCode);
        $localeCode = $this->getLocaleCode($localeCode, $channel);

        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonRepository->findOneBy(['code' => $taxonCode]);

        Assert::notNull($taxon, sprintf('Taxon with code %s has not been found', $taxonCode));
        $paginatorDetails->addToParameters('code', $taxonCode);

        return $this->findByTaxon($taxon, $channel, $paginatorDetails, $localeCode);
    }

    /**
     * @param string $localeCode
     * @param iterable|LocaleInterface[] $supportedLocales
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

    private function findByTaxon(TaxonInterface $taxon, ChannelInterface $channel, PaginatorDetails $paginatorDetails, string $localeCode): PageView
    {
        $queryBuilder = $this->productRepository->createShopListQueryBuilder($channel, $taxon, $localeCode);
        $queryBuilder->addOrderBy('o.id');

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));

        $pagerfanta->setMaxPerPage($paginatorDetails->limit());
        $pagerfanta->setCurrentPage($paginatorDetails->page());

        $pageView = $this->pageViewFactory->create($pagerfanta, $paginatorDetails->route(), $paginatorDetails->parameters());

        foreach ($pagerfanta->getCurrentPageResults() as $currentPageResult) {
            $pageView->items[] = $this->productViewFactory->create($currentPageResult, $channel, $localeCode);
        }

        return $pageView;
    }
}
