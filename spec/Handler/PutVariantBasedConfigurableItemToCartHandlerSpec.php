<?php

namespace spec\Sylius\ShopApiPlugin\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Command\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Handler\PutVariantBasedConfigurableItemToCartHandler;
use PhpSpec\ObjectBehavior;

final class PutVariantBasedConfigurableItemToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderProcessorInterface $orderProcessor,
        ObjectManager $manager
    ) {
        $this->beConstructedWith($cartRepository, $productVariantRepository, $cartItemFactory, $orderItemModifier, $orderProcessor, $manager);
    }

    function it_handles_putting_new_item_to_cart(
        OrderItemInterface $cartItem,
        OrderInterface $cart,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderProcessorInterface $orderProcessor,
        OrderRepositoryInterface $cartRepository,
        ProductVariantInterface $productVariant,
        ProductVariantRepositoryInterface $productVariantRepository,
        ObjectManager $manager
    ) {
        $productVariantRepository->findOneByCodeAndProductCode('RED_SMALL_T_SHIRT_CODE', 'T_SHIRT_CODE')->willReturn($productVariant);

        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $cartItemFactory->createForCart($cart)->willReturn($cartItem);

        $cartItem->setVariant($productVariant)->shouldBeCalled();
        $orderItemModifier->modify($cartItem, 5)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalled();

        $manager->persist($cart)->shouldBeCalled();

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
