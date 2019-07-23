<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\DependencyInjection;

use Sylius\ShopApiPlugin\Request\Cart\DropCartRequest;
use Sylius\ShopApiPlugin\Request\Cart\PickupCartRequest;
use Sylius\ShopApiPlugin\Request\Product\AddProductReviewByCodeRequest;
use Sylius\ShopApiPlugin\Request\Product\AddProductReviewBySlugRequest;
use Sylius\ShopApiPlugin\View;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_shop_api');

        $this->buildIncludedAttributesNode($rootNode);
        $this->buildViewClassesNode($rootNode);
        $this->buildRequestClassesNode($rootNode);

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
                        ->scalarNode('address')->defaultValue(View\AddressBook\AddressView::class)->end()
                        ->scalarNode('address_book')->defaultValue(View\AddressBook\AddressBookView::class)->end()
                        ->scalarNode('adjustment')->defaultValue(View\Cart\AdjustmentView::class)->end()
                        ->scalarNode('cart_item')->defaultValue(View\ItemView::class)->end()
                        ->scalarNode('cart_summary')->defaultValue(View\Cart\CartSummaryView::class)->end()
                        ->scalarNode('customer')->defaultValue(View\Customer\CustomerView::class)->end()
                        ->scalarNode('estimated_shipping_cost')->defaultValue(View\Cart\EstimatedShippingCostView::class)->end()
                        ->scalarNode('image')->defaultValue(View\Taxon\ImageView::class)->end()
                        ->scalarNode('page')->defaultValue(View\Product\PageView::class)->end()
                        ->scalarNode('page_links')->defaultValue(View\Product\PageLinksView::class)->end()
                        ->scalarNode('payment')->defaultValue(View\Cart\PaymentView::class)->end()
                        ->scalarNode('payment_method')->defaultValue(View\Cart\PaymentMethodView::class)->end()
                        ->scalarNode('price')->defaultValue(View\PriceView::class)->end()
                        ->scalarNode('placed_order')->defaultValue(View\Order\PlacedOrderView::class)->end()
                        ->scalarNode('product')->defaultValue(View\Product\ProductView::class)->end()
                        ->scalarNode('product_attribute_value')->defaultValue(View\Product\ProductAttributeValueView::class)->end()
                        ->scalarNode('product_review')->defaultValue(View\Product\ProductReviewView::class)->end()
                        ->scalarNode('product_taxon')->defaultValue(View\Product\ProductTaxonView::class)->end()
                        ->scalarNode('product_variant')->defaultValue(View\Product\ProductVariantView::class)->end()
                        ->scalarNode('shipment')->defaultValue(View\Checkout\ShipmentView::class)->end()
                        ->scalarNode('shipping_method')->defaultValue(View\Cart\ShippingMethodView::class)->end()
                        ->scalarNode('taxon')->defaultValue(View\Taxon\TaxonView::class)->end()
                        ->scalarNode('taxon_details')->defaultValue(View\Taxon\TaxonDetailsView::class)->end()
                        ->scalarNode('totals')->defaultValue(View\Cart\TotalsView::class)->end()
                        ->scalarNode('validation_error')->defaultValue(View\ValidationErrorView::class)->end()
                        ->scalarNode('variant_option')->defaultValue(View\Product\VariantOptionView::class)->end()
                        ->scalarNode('variant_option_value')->defaultValue(View\Product\VariantOptionValueView::class)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function buildRequestClassesNode(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('request_classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('add_product_review_by_code')->defaultValue(AddProductReviewByCodeRequest::class)->end()
                        ->scalarNode('add_product_review_by_slug')->defaultValue(AddProductReviewBySlugRequest::class)->end()
                        ->scalarNode('drop_cart')->defaultValue(DropCartRequest::class)->end()
                        ->scalarNode('pickup_cart')->defaultValue(PickupCartRequest::class)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
