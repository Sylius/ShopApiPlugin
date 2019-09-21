<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Modifier\OrderModifierInterface;

final class PutVariantBasedConfigurableItemToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderModifierInterface $orderModifier
    ): void {
        $this->beConstructedWith($cartRepository, $productVariantRepository, $orderModifier);
    }

    function it_handles_putting_new_item_to_cart(
        OrderInterface $cart,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $cartRepository,
        ProductVariantInterface $productVariant,
        ProductVariantRepositoryInterface $productVariantRepository
    ): void {
        $productVariantRepository->findOneByCodeAndProductCode('RED_SMALL_T_SHIRT_CODE', 'T_SHIRT_CODE')->willReturn($productVariant);

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
        ProductVariantRepositoryInterface $productVariantRepository
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $productVariantRepository->findOneByCodeAndProductCode('RED_SMALL_T_SHIRT_CODE', 'T_SHIRT_CODE')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PutVariantBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 'RED_SMALL_T_SHIRT_CODE', 5),
        ]);
    }
}
