<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Handler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\SyliusShopApiPlugin\Command\PutVariantBasedConfigurableItemToCart;
use Sylius\SyliusShopApiPlugin\Modifier\OrderModifierInterface;

final class PutVariantBasedConfigurableItemToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderModifierInterface $orderModifier
    ) {
        $this->beConstructedWith($cartRepository, $productVariantRepository, $orderModifier);
    }

    function it_handles_putting_new_item_to_cart(
        OrderInterface $cart,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $cartRepository,
        ProductVariantInterface $productVariant,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $productVariantRepository->findOneByCodeAndProductCode('RED_SMALL_T_SHIRT_CODE', 'T_SHIRT_CODE')->willReturn($productVariant);

        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $orderModifier->modify($cart, $productVariant, 5)->shouldBeCalled();

        $this->handle(new PutVariantBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 'RED_SMALL_T_SHIRT_CODE', 5));
    }

    function it_throws_an_exception_if_cart_has_not_been_found(OrderRepositoryInterface $cartRepository)
    {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new PutVariantBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 'RED_SMALL_T_SHIRT_CODE', 5),
        ]);
    }

    function it_throws_an_exception_if_product_has_not_been_found(
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $productVariantRepository->findOneByCodeAndProductCode('RED_SMALL_T_SHIRT_CODE', 'T_SHIRT_CODE')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new PutVariantBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 'RED_SMALL_T_SHIRT_CODE', 5),
        ]);
    }
}
