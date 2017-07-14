<?php

namespace Tests\Sylius\ShopApiPlugin\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\ShopApiPlugin\DependencyInjection\ShopApiExtension;
use Sylius\ShopApiPlugin\View;

final class ShopApiExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_sets_up_parameter_with_attributes_to_serialize(): void
    {
        $this->load([
            'included_attributes' => [
                'ATTRIBUTE_CODE',
            ],
        ]);

        $this->assertContainerBuilderHasParameter('sylius.shop_api.included_attributes', ['ATTRIBUTE_CODE']);
    }

    /**
     * @test
     */
    public function it_defines_view_classes_parameters(): void
    {
        $this->load([]);

        $nameToClass = [
            'address' => View\AddressView::class,
            'adjustment' => View\AdjustmentView::class,
            'cart_item' => View\ItemView::class,
            'cart_summary' => View\CartSummaryView::class,
            'estimated_shipping_cost' => View\EstimatedShippingCostView::class,
            'image' => View\ImageView::class,
            'page' => View\PageView::class,
            'page_links' => View\PageLinksView::class,
            'payment' => View\PaymentView::class,
            'payment_method' => View\PaymentMethodView::class,
            'price' => View\PriceView::class,
            'product' => View\ProductView::class,
            'product_attribute_value' => View\ProductAttributeValueView::class,
            'product_review' => View\ProductReviewView::class,
            'product_taxon' => View\ProductTaxonView::class,
            'product_variant' => View\ProductVariantView::class,
            'shipment' => View\ShipmentView::class,
            'shipping_method' => View\ShippingMethodView::class,
            'taxon' => View\TaxonView::class,
            'taxon_details' => View\TaxonDetailsView::class,
            'totals' => View\TotalsView::class,
            'validation_error' => View\ValidationErrorView::class,
            'variant_option' => View\VariantOptionView::class,
            'variant_option_value' => View\VariantOptionValueView::class,
        ];

        foreach ($nameToClass as $name => $class) {
            $this->assertContainerBuilderHasParameter(
                sprintf('sylius.shop_api.view.%s.class', $name),
                $class
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [new ShopApiExtension()];
    }
}
