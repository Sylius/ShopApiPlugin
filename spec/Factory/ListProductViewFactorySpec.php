<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\SyliusShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Factory\ProductVariantViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Factory\ViewCreationException;
use Sylius\SyliusShopApiPlugin\View\ProductVariantView;
use Sylius\SyliusShopApiPlugin\View\ProductView;

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
        $product->getVariants()->willReturn(new ArrayCollection([
            $firstProductVariant->getWrappedObject(),
            $secondProductVariant->getWrappedObject(),
        ]));
        $product->getImages()->willReturn(new ArrayCollection([]));
        $product->getAssociations()->willReturn(new ArrayCollection([$productAssociation->getWrappedObject()]));

        $firstProductVariant->getCode()->willReturn('S_HAT_CODE');
        $secondProductVariant->getCode()->willReturn('L_HAT_CODE');
        $associatedProductVariant->getCode()->willReturn('SMALL_MUG_CODE');

        $productAssociation->getType()->willReturn($associationType);
        $productAssociation->getAssociatedProducts()->willReturn(new ArrayCollection([$associatedProduct->getWrappedObject()]));
        $associatedProduct->getVariants()->willReturn(new ArrayCollection([$associatedProductVariant->getWrappedObject()]));

        $associatedProduct->getImages()->willReturn(new ArrayCollection([]));

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
        $product->getVariants()->willReturn(new ArrayCollection([
            $firstProductVariant->getWrappedObject(),
            $secondProductVariant->getWrappedObject(),
        ]));
        $product->getImages()->willReturn(new ArrayCollection([]));
        $product->getAssociations()->willReturn(new ArrayCollection([$productAssociation->getWrappedObject()]));

        $firstProductVariant->getCode()->willReturn('S_HAT_CODE');
        $secondProductVariant->getCode()->willReturn('L_HAT_CODE');
        $thirdProductVariant->getCode()->willReturn('XL_HAT_CODE');
        $associatedProductVariant->getCode()->willReturn('SMALL_MUG_CODE');

        $productAssociation->getType()->willReturn($associationType);
        $productAssociation->getAssociatedProducts()->willReturn(new ArrayCollection([$associatedProduct->getWrappedObject()]));
        $associatedProduct->getVariants()->willReturn(new ArrayCollection([$associatedProductVariant->getWrappedObject()]));

        $associatedProduct->getImages()->willReturn(new ArrayCollection([]));

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
