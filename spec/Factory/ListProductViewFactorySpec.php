<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\ShopApiPlugin\Factory\DetailedProductViewFactory;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductVariantViewFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ViewCreationException;
use Sylius\ShopApiPlugin\Generator\ProductBreadcrumbGeneratorInterface;
use Sylius\ShopApiPlugin\View\ImageView;
use Sylius\ShopApiPlugin\View\ProductVariantView;
use Sylius\ShopApiPlugin\View\ProductView;

final class ListProductViewFactorySpec extends ObjectBehavior
{
    function let(
        ImageViewFactoryInterface $imageViewFactory,
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantViewFactoryInterface $variantViewFactory
    ) {
        $this->beConstructedWith($imageViewFactory, $productViewFactory, $variantViewFactory);
    }

    function it_is_product_view_factory()
    {
        $this->shouldHaveType(ProductViewFactoryInterface::class);
    }

    function it_builds_product_view_with_variants_and_associations(
        ChannelInterface $channel,
        ProductAssociationInterface $productAssociation,
        ProductInterface $product,
        ProductInterface $associatedProduct,
        ProductAssociationTypeInterface $associationType,
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantInterface $associatedProductVariant,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        ProductVariantViewFactoryInterface $variantViewFactory
    ) {
        $product->getVariants()->willReturn(new ArrayCollection([$firstProductVariant->getWrappedObject(), $secondProductVariant->getWrappedObject()]));
        $product->getImages()->willReturn(new ArrayCollection());
        $product->getAssociations()->willReturn(new ArrayCollection([$productAssociation->getWrappedObject()]));

        $firstProductVariant->getCode()->willReturn('S_HAT_CODE');
        $secondProductVariant->getCode()->willReturn('L_HAT_CODE');
        $associatedProductVariant->getCode()->willReturn('SMALL_MUG_CODE');

        $productAssociation->getType()->willReturn($associationType);
        $productAssociation->getAssociatedProducts()->willReturn(new ArrayCollection([$associatedProduct->getWrappedObject()]));
        $associatedProduct->getVariants()->willReturn(new ArrayCollection([$associatedProductVariant->getWrappedObject()]));

        $associatedProduct->getImages()->willReturn(new ArrayCollection());

        $associationType->getCode()->willReturn('ASSOCIATION_TYPE');

        $productViewFactory->create($product, $channel, 'en_GB')->willReturn(new ProductView());
        $productViewFactory->create($associatedProduct, $channel, 'en_GB')->willReturn(new ProductView());

        $variantViewFactory->create($firstProductVariant, $channel, 'en_GB')->willReturn(new ProductVariantView());
        $variantViewFactory->create($secondProductVariant, $channel, 'en_GB')->willReturn(new ProductVariantView());
        $variantViewFactory->create($associatedProductVariant, $channel, 'en_GB')->willReturn(new ProductVariantView());

        $associatedProductView = new ProductView();
        $associatedProductView->variants = [
            'SMALL_MUG_CODE' => new ProductVariantView(),
        ];

        $productView = new ProductView();
        $productView->variants = [
            'S_HAT_CODE' => new ProductVariantView(),
            'L_HAT_CODE' => new ProductVariantView(),
        ];
        $productView->associations = [
            'ASSOCIATION_TYPE' => [
                $associatedProductView,
            ],
        ];

        $this->create($product, $channel, 'en_GB')->shouldBeLike($productView);
    }

    function it_skips_invalid_product_variants(
        ChannelInterface $channel,
        ProductAssociationInterface $productAssociation,
        ProductInterface $product,
        ProductInterface $associatedProduct,
        ProductAssociationTypeInterface $associationType,
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantInterface $associatedProductVariant,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        ProductVariantInterface $thirdProductVariant,
        ProductVariantViewFactoryInterface $variantViewFactory
    ) {
        $product->getVariants()->willReturn([$firstProductVariant, $secondProductVariant]);
        $product->getImages()->willReturn([]);
        $product->getAssociations()->willReturn([$productAssociation]);

        $firstProductVariant->getCode()->willReturn('S_HAT_CODE');
        $secondProductVariant->getCode()->willReturn('L_HAT_CODE');
        $thirdProductVariant->getCode()->willReturn('XL_HAT_CODE');
        $associatedProductVariant->getCode()->willReturn('SMALL_MUG_CODE');

        $productAssociation->getType()->willReturn($associationType);
        $productAssociation->getAssociatedProducts()->willReturn([$associatedProduct]);
        $associatedProduct->getVariants()->willReturn([$associatedProductVariant]);

        $associatedProduct->getImages()->willReturn([]);

        $associationType->getCode()->willReturn('ASSOCIATION_TYPE');

        $productViewFactory->create($product, $channel, 'en_GB')->willReturn(new ProductView());
        $productViewFactory->create($associatedProduct, $channel, 'en_GB')->willReturn(new ProductView());

        $variantViewFactory->create($firstProductVariant, $channel, 'en_GB')->willReturn(new ProductVariantView());
        $variantViewFactory->create($secondProductVariant, $channel, 'en_GB')->willReturn(new ProductVariantView());
        $variantViewFactory->create($thirdProductVariant, $channel, 'en_GB')->willThrow(ViewCreationException::class);
        $variantViewFactory->create($associatedProductVariant, $channel, 'en_GB')->willReturn(new ProductVariantView());

        $associatedProductView = new ProductView();
        $associatedProductView->variants = [
            'SMALL_MUG_CODE' => new ProductVariantView(),
        ];

        $productView = new ProductView();
        $productView->variants = [
            'S_HAT_CODE' => new ProductVariantView(),
            'L_HAT_CODE' => new ProductVariantView(),
        ];
        $productView->associations = [
            'ASSOCIATION_TYPE' => [
                $associatedProductView,
            ],
        ];

        $this->create($product, $channel, 'en_GB')->shouldBeLike($productView);
    }
}
