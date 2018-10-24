<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Modifier\OrderModifierInterface;

final class PutSimpleItemToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        OrderModifierInterface $orderModifier
    ): void {
        $this->beConstructedWith($cartRepository, $productRepository, $orderModifier);
    }

    function it_handles_putting_new_item_to_cart(
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        ProductInterface $product,
        ProductRepositoryInterface $productRepository,
        ProductVariantInterface $productVariant,
        OrderModifierInterface $orderModifier
    ): void {
        $productRepository->findOneBy(['code' => 'T_SHIRT_CODE'])->willReturn($product);
        $product->getVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));
        $product->isSimple()->willReturn(true);

        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $orderModifier->modify($cart, $productVariant, 5)->shouldBeCalled();

        $this->handle(new PutSimpleItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 5));
    }

    function it_throws_an_exception_if_cart_has_not_been_found(OrderRepositoryInterface $cartRepository): void
    {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new PutSimpleItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 5),
        ]);
    }

    function it_throws_an_exception_if_product_has_not_been_found(
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $productRepository->findOneBy(['code' => 'T_SHIRT_CODE'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new PutSimpleItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 5),
        ]);
    }

    function it_throws_an_exception_if_product_is_configurable(
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        ProductInterface $product,
        ProductRepositoryInterface $productRepository,
        ProductVariantInterface $productVariant
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $productRepository->findOneBy(['code' => 'T_SHIRT_CODE'])->willReturn($product);
        $product->getVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));
        $product->isSimple()->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new PutSimpleItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 5),
        ]);
    }
}
