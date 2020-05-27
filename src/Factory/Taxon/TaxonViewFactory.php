<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Taxon;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\Transformer\Transformer;
use Sylius\ShopApiPlugin\View\Taxon\TaxonView;
use Sylius\ShopApiPlugin\ViewRepository\Product\ProductCatalogViewRepository;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ListProductViewFactory;

final class TaxonViewFactory implements TaxonViewFactoryInterface
{

    use Transformer;

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

    /** @var ProductVariantViewFactoryInterface */
    private $variantViewFactory;
    private $locale;
    private $channel;
    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * TaxonViewFactory constructor.
     *
     * @param ImageViewFactoryInterface          $imageViewFactory
     * @param string                             $taxonViewClass
     * @param ProductCatalogViewRepository       $productCatalogQuery
     * @param ChannelContextInterface            $channelContext
     * @param ProductRepositoryInterface         $productRepository
     * @param ProductVariantViewFactoryInterface $variantViewFactory
     * @param TaxonRepositoryInterface           $taxonRepository
     */
    public function __construct(
        ImageViewFactoryInterface $imageViewFactory,
        string $taxonViewClass,
        ProductCatalogViewRepository $productCatalogQuery,
        ChannelContextInterface $channelContext,
        ProductRepositoryInterface $productRepository,
        ProductVariantViewFactoryInterface $variantViewFactory,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $this->imageViewFactory    = $imageViewFactory;
        $this->taxonViewClass      = $taxonViewClass;
        $this->viewClass           = $taxonViewClass;
        $this->productCatalogQuery = $productCatalogQuery;
        $this->channelContext      = $channelContext;
        $this->productRepository   = $productRepository;
        $this->variantViewFactory  = $variantViewFactory;
        $this->taxonRepository     = $taxonRepository;
    }

    public $defaultIncludes
        = [
            'code',
            'position',
            'name',
            'slug',
            'description',
//        'countOfProducts',
//        'cheapestProduct',
            'metaTitle',
            'metaDescription',
            'images',
        ];

    public function create(TaxonInterface $taxon, string $locale): TaxonView
    {
        $this->locale  = $locale;
        $this->channel = $this->channelContext->getChannel();

        /** @var TaxonView $taxonView */
        $taxonView = $this->generate($taxon);

        return $taxonView;
    }

    protected function getCode(TaxonInterface $taxon, TaxonView $taxonView)
    {
        $taxonView->code = $taxon->getCode();

        return $taxonView;
    }

    protected function getPosition(TaxonInterface $taxon, TaxonView $taxonView)
    {
        $taxonView->position = $taxon->getPosition();

        return $taxonView;
    }

    protected function getName(TaxonInterface $taxon, TaxonView $taxonView)
    {
        /** @var TaxonTranslationInterface $taxonTranslation */
        $taxonTranslation = $taxon->getTranslation($this->locale);
        $taxonView->name  = $taxonTranslation->getName();

        return $taxonView;
    }

    protected function getSlug(TaxonInterface $taxon, TaxonView $taxonView)
    {
        /** @var TaxonTranslationInterface $taxonTranslation */
        $taxonTranslation = $taxon->getTranslation($this->locale);
        $taxonView->slug  = $taxonTranslation->getSlug();

        return $taxonView;
    }

    protected function getDescription(TaxonInterface $taxon, TaxonView $taxonView)
    {
        $taxonTranslation       = $taxon->getTranslation($this->locale);
        $taxonView->description = $taxonTranslation->getDescription();

        return $taxonView;
    }

    protected function getCountOfProducts(TaxonInterface $taxon, TaxonView $taxonView)
    {
        $taxonView->countOfProducts = $this->productCatalogQuery->getCountByTaxon($taxon, $this->locale);

        return $taxonView;
    }

    protected function getCheapestProduct(TaxonInterface $taxon, TaxonView $taxonView)
    {
        $variant50g = $this->productRepository->find50gProductByChannel($this->channel, $this->locale, $taxon);

        if ($variant50g) {
            $taxonView->cheapestProduct
                = $this->variantViewFactory->create($variant50g[0], $this->channel, $this->locale);
        }

        return $taxonView;
    }

    protected function getImages(TaxonInterface $taxon, TaxonView $taxonView)
    {
        /** @var ImageInterface $image */
        foreach ($taxon->getImages() as $image) {
            $taxonView->images[] = $this->imageViewFactory->create($image);
        }

        return $taxonView;
    }

    protected function getMetaTitle(TaxonInterface $taxon, TaxonView $taxonView)
    {
        $taxonView->metaTitle = $taxon->getTranslation()->getMetaTitle();

        return $taxonView;
    }

    protected function getMetaDescription(TaxonInterface $taxon, TaxonView $taxonView)
    {
        $taxonView->metaDescription = $taxon->getTranslation()->getMetaDescription();

        return $taxonView;
    }

    /**
     * @param TaxonInterface $taxon
     * @param string         $prev
     *
     * @return string
     */
    public function getCanonicalUrl(TaxonInterface $taxon, TaxonView $taxonView)
    {
        if ($this->isTag($taxon)) {
            $taxonView->canonicalUrl = $this->getCanonicalUrlForTagFunc($taxon);
        } else {
            $taxonView->canonicalUrl = $this->getCanonicalUrlForTaxonFunc($taxon);
        }

        return $taxonView;
    }

    private function isTag(TaxonInterface $taxon)
    {
        $isTag = false;
        if ($taxon->getParent()) {
            if ($taxon->getParent()->getCode() == 'tags') {
                return true;
            }
            $isTag = $this->isTag($taxon->getParent());
        }

        return $isTag;
    }

    /**
     * @param TaxonInterface $taxon
     * @param string         $prev
     *
     * @return string
     */
    private function getCanonicalUrlForTaxonFunc(TaxonInterface $taxon, $prev = '')
    {
        if ($taxon->getParent() && ! in_array($taxon->getParent()->getCode(), ['category', 'tags'])) {
            $prev .= $this->getCanonicalUrlForTaxonFunc($taxon->getParent(), $prev);
        }

        $prev .= '/' . $taxon->getCode();

        return $prev;
    }

    /**
     * @param TaxonInterface $taxon
     * @param string         $prev
     *
     * @return string
     */
    private function getCanonicalUrlForTagFunc(TaxonInterface $taxon, $prev = '')
    {
        $taxonCode = $taxon->getCode();
        if (strpos($taxon->getCode(), '_tag') === false) {
            $taxonCode = $taxon->getParent()->getCode();
        }

        /** @var TaxonInterface $taxonForTag */
        $taxonForTag          = $this->taxonRepository->findOneBy(
            ['code' => str_replace('_tag', '', $taxonCode)]
        );
        $canonicalUrlForTaxon = $this->getCanonicalUrlForTaxonFunc($taxonForTag);

        if ($canonicalUrlForTaxon) {
            $prev = $canonicalUrlForTaxon . '/tag/' . $taxon->getCode();
        }

        return $prev;
    }
}
