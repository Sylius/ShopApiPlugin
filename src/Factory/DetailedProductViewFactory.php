<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ShopApiPlugin\Generator\ProductBreadcrumbGeneratorInterface;
use Sylius\ShopApiPlugin\View\ProductView;

final class DetailedProductViewFactory implements ProductViewFactoryInterface
{
    /** @var ProductViewFactoryInterface */
    private $productViewFactory;

    /** @var ProductBreadcrumbGeneratorInterface */
    private $breadcrumbGenerator;

    public function __construct(
        ProductViewFactoryInterface $productViewFactory,
        ProductBreadcrumbGeneratorInterface $breadcrumbGenerator
    ) {
        $this->productViewFactory = $productViewFactory;
        $this->breadcrumbGenerator = $breadcrumbGenerator;
    }

    public function create(ProductInterface $product, ChannelInterface $channel, string $locale): ProductView
    {
        $productView = $this->productViewFactory->create($product, $channel, $locale);
        $productView->breadcrumb = $this->breadcrumbGenerator->generate($product, $locale);

        return $productView;
    }
}
