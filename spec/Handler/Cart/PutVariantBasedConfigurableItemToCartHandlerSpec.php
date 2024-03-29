<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\ShopApiPlugin\Checker\ProductInCartChannelCheckerInterface;
use Sylius\ShopApiPlugin\Command\Cart\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Modifier\OrderModifierInterface;

final class PutVariantBasedConfigurableItemToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderModifierInterface $orderModifier,
        ProductInCartChannelCheckerInterface $channelChecker,
    ): void {
        $this->beConstructedWith($cartRepository, $productVariantRepository, $orderModifier, $channelChecker);
    }

    function it_handles_putting_new_item_to_cart(
        OrderInterface $cart,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $cartRepository,
        ProductInCartChannelCheckerInterface $channelChecker,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ProductVariantRepositoryInterface $productVariantRepository,
    ): void {
        $productVariantRepository->findOneByCodeAndProductCode('RED_SMALL_T_SHIRT_CODE', 'T_SHIRT_CODE')->willReturn($productVariant);
        $productVariant->getProduct()->willReturn($product);

        $channelChecker->isProductInCartChannel($product, $cart)->willReturn(true);

        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $orderModifier->modify($cart, $productVariant, 5)->shouldBeCalled();

        $this(new PutVariantBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 'RED_SMALL_T_SHIRT_CODE', 5));
    }

    function it_throws_an_exception_if_cart_has_not_been_found(OrderRepositoryInterface $cartRepository): void
    {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PutVariantBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 'RED_SMALL_T_SHIRT_CODE', 5),
        ]);
    }

    function it_throws_an_exception_if_product_has_not_been_found(
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $productVariantRepository->findOneByCodeAndProductCode('RED_SMALL_T_SHIRT_CODE', 'T_SHIRT_CODE')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PutVariantBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 'RED_SMALL_T_SHIRT_CODE', 5),
        ]);
    }

    function it_throws_an_exception_if_product_has_different_channel_than_cart(
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        ProductInCartChannelCheckerInterface $channelChecker,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
    ): void {
        $productVariantRepository->findOneByCodeAndProductCode('RED_SMALL_T_SHIRT_CODE', 'T_SHIRT_CODE')->willReturn($productVariant);
        $productVariant->getProduct()->willReturn($product);

        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $channelChecker->isProductInCartChannel($product, $cart)->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PutVariantBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 'RED_SMALL_T_SHIRT_CODE', 5),
        ]);
    }
}
