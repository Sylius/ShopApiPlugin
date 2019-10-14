<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Taxon;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Taxon\TaxonView;
use Sylius\ShopApiPlugin\ViewRepository\Product\ProductCatalogViewRepository;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ListProductViewFactory;

final class TaxonViewFactory implements TaxonViewFactoryInterface
{

    /** @var ImageViewFactoryInterface */
    private $imageViewFactory;

    /** @var string */
    private $taxonViewClass;

    /** @var ProductCatalogViewRepository */
    private $productCatalogQuery;

    /** @var ChannelContextInterface */
    protected $channelContext;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(
        ImageViewFactoryInterface $imageViewFactory,
        string $taxonViewClass,
        ProductCatalogViewRepository $productCatalogQuery,
        ChannelContextInterface $channelContext,
        ProductRepositoryInterface $productRepository,
        ListProductViewFactory $productViewFactory
    ) {
        $this->imageViewFactory    = $imageViewFactory;
        $this->taxonViewClass      = $taxonViewClass;
        $this->productCatalogQuery = $productCatalogQuery;
        $this->channelContext      = $channelContext;
        $this->productRepository   = $productRepository;
        $this->productViewFactory  = $productViewFactory;
    }

    public function create(TaxonInterface $taxon, string $locale): TaxonView
    {
        $channel          = $this->channelContext->getChannel();
        $cheapestProducts = $this->productRepository->findCheapestByChannel($channel, $locale, $taxon)->getQuery()->getResult();

        /** @var TaxonTranslationInterface $taxonTranslation */
        $taxonTranslation = $taxon->getTranslation($locale);

        /** @var TaxonView $taxonView */
        $taxonView = new $this->taxonViewClass();

        $taxonView->code     = $taxon->getCode();
        $taxonView->position = $taxon->getPosition();

        $taxonView->name            = $taxonTranslation->getName();
        $taxonView->slug            = $taxonTranslation->getSlug();
        $taxonView->description     = $taxonTranslation->getDescription();
        $taxonView->countOfProducts = $this->productCatalogQuery->getCountByTaxon($taxon, $locale);
        if($cheapestProducts){
            $taxonView->cheapestProduct = $this->productViewFactory->create($cheapestProducts[0], $channel, $locale);
        }
        /** @var ImageInterface $image */
        foreach ($taxon->getImages() as $image) {
            $taxonView->images[] = $this->imageViewFactory->create($image);
        }

        return $taxonView;
    }
}
