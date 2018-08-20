<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\DependencyInjection;

use Sylius\SyliusShopApiPlugin\View;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('shop_api');

        $this->buildIncludedAttributesNode($rootNode);
        $this->buildViewClassesNode($rootNode);

        return $treeBuilder;
    }

    private function buildIncludedAttributesNode(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('included_attributes')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
    }

    private function buildViewClassesNode(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('view_classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('address')->defaultValue(View\AddressView::class)->end()
                        ->scalarNode('adjustment')->defaultValue(View\AdjustmentView::class)->end()
                        ->scalarNode('cart_item')->defaultValue(View\ItemView::class)->end()
                        ->scalarNode('cart_summary')->defaultValue(View\CartSummaryView::class)->end()
                        ->scalarNode('customer')->defaultValue(View\CustomerView::class)->end()
                        ->scalarNode('estimated_shipping_cost')->defaultValue(View\EstimatedShippingCostView::class)->end()
                        ->scalarNode('image')->defaultValue(View\ImageView::class)->end()
                        ->scalarNode('page')->defaultValue(View\PageView::class)->end()
                        ->scalarNode('page_links')->defaultValue(View\PageLinksView::class)->end()
                        ->scalarNode('payment')->defaultValue(View\PaymentView::class)->end()
                        ->scalarNode('payment_method')->defaultValue(View\PaymentMethodView::class)->end()
                        ->scalarNode('price')->defaultValue(View\PriceView::class)->end()
                        ->scalarNode('product')->defaultValue(View\ProductView::class)->end()
                        ->scalarNode('product_attribute_value')->defaultValue(View\ProductAttributeValueView::class)->end()
                        ->scalarNode('product_review')->defaultValue(View\ProductReviewView::class)->end()
                        ->scalarNode('product_taxon')->defaultValue(View\ProductTaxonView::class)->end()
                        ->scalarNode('product_variant')->defaultValue(View\ProductVariantView::class)->end()
                        ->scalarNode('shipment')->defaultValue(View\ShipmentView::class)->end()
                        ->scalarNode('shipping_method')->defaultValue(View\ShippingMethodView::class)->end()
                        ->scalarNode('taxon')->defaultValue(View\TaxonView::class)->end()
                        ->scalarNode('taxon_details')->defaultValue(View\TaxonDetailsView::class)->end()
                        ->scalarNode('totals')->defaultValue(View\TotalsView::class)->end()
                        ->scalarNode('validation_error')->defaultValue(View\ValidationErrorView::class)->end()
                        ->scalarNode('variant_option')->defaultValue(View\VariantOptionView::class)->end()
                        ->scalarNode('variant_option_value')->defaultValue(View\VariantOptionValueView::class)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
