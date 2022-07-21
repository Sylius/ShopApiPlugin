<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Checker\ProductInCartChannelCheckerInterface;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Modifier\OrderModifierInterface;

final class PutSimpleItemToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        OrderModifierInterface $orderModifier,
        ProductInCartChannelCheckerInterface $channelChecker,
    ): void {
        $this->beConstructedWith($cartRepository, $productRepository, $orderModifier, $channelChecker);
    }

    function it_handles_putting_new_item_to_cart(
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        ProductInterface $product,
        ProductRepositoryInterface $productRepository,
        ProductVariantInterface $productVariant,
        OrderModifierInterface $orderModifier,
        ProductInCartChannelCheckerInterface $channelChecker,
    ): void {
        $productRepository->findOneBy(['code' => 'T_SHIRT_CODE'])->willReturn($product);
        $product->getVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));
        $product->isSimple()->willReturn(true);

        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $channelChecker->isProductInCartChannel($product, $cart)->willReturn(true);

        $orderModifier->modify($cart, $productVariant, 5)->shouldBeCalled();

        $this(new PutSimpleItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 5));
    }

    function it_throws_an_exception_if_cart_has_not_been_found(OrderRepositoryInterface $cartRepository): void
    {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PutSimpleItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 5),
        ]);
    }

    function it_throws_an_exception_if_product_has_not_been_found(
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $productRepository->findOneBy(['code' => 'T_SHIRT_CODE'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PutSimpleItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 5),
        ]);
    }

    function it_throws_an_exception_if_product_is_configurable(
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        ProductInCartChannelCheckerInterface $channelChecker,
        ProductInterface $product,
        ProductRepositoryInterface $productRepository,
        ProductVariantInterface $productVariant,
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $productRepository->findOneBy(['code' => 'T_SHIRT_CODE'])->willReturn($product);
        $product->getVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));

        $channelChecker->isProductInCartChannel($product, $cart)->willReturn(true);

        $product->isSimple()->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PutSimpleItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 5),
        ]);
    }

    function it_throws_an_exception_if_product_is_not_in_same_channel_as_cart(
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        ProductInCartChannelCheckerInterface $channelChecker,
        ProductInterface $product,
        ProductRepositoryInterface $productRepository,
        ProductVariantInterface $productVariant,
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $productRepository->findOneBy(['code' => 'T_SHIRT_CODE'])->willReturn($product);
        $product->getVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));
        $product->isSimple()->willReturn(false);

        $channelChecker->isProductInCartChannel($product, $cart)->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during(
            '__invoke',
            [
            new PutSimpleItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 5),
        ],
        );
    }
}
