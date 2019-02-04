<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\DependencyInjection\Configuration;
use Sylius\ShopApiPlugin\View;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_processed_attribute_codes_which_should_be_serialized(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[
                'included_attributes' => [
                    'FIRST_CODE',
                    'SECOND_CODE',
                ],
            ]],
            [
                'included_attributes' => [
                    'FIRST_CODE',
                    'SECOND_CODE',
                ],
            ],
            'included_attributes'
        );
    }

    /**
     * @test
     */
    public function it_processed_empty_attribute_codes_list_and_returns_empty_array(): void
    {
        $this->assertProcessedConfigurationEquals([], ['included_attributes' => []], 'included_attributes');
    }

    /**
     * @test
     */
    public function it_has_view_classes(): void
    {
        $this->assertProcessedConfigurationEquals([], [
            'view_classes' => [
                'address' => View\AddressView::class,
                'address_book' => View\AddressBookView::class,
                'adjustment' => View\AdjustmentView::class,
                'cart_item' => View\ItemView::class,
                'cart_summary' => View\CartSummaryView::class,
                'customer' => View\CustomerView::class,
                'estimated_shipping_cost' => View\EstimatedShippingCostView::class,
                'image' => View\ImageView::class,
                'page' => View\PageView::class,
                'page_links' => View\PageLinksView::class,
                'payment' => View\PaymentView::class,
                'payment_method' => View\PaymentMethodView::class,
        'placed_order' => View\PlacedOrderView::class,
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
            ],
        ], 'view_classes');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
