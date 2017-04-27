<?php

namespace spec\Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Handler\PutSimpleItemToCartHandler;
use PhpSpec\ObjectBehavior;

final class PutSimpleItemToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->beConstructedWith($cartRepository, $productRepository, $cartItemFactory, $orderItemModifier, $orderProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PutSimpleItemToCartHandler::class);
    }

    function it_handles_putting_new_item_to_cart(
        OrderItemInterface $cartItem,
        OrderInterface $cart,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderProcessorInterface $orderProcessor,
        OrderRepositoryInterface $cartRepository,
        ProductInterface $product,
        ProductRepositoryInterface $productRepository,
        ProductVariantInterface $productVariant
    ) {
        $productRepository->findOneBy(['code' => 'T_SHIRT_CODE'])->willReturn($product);
        $product->getVariants()->willReturn([$productVariant]);
        $product->isSimple()->willReturn(true);

        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $cartItemFactory->createForCart($cart)->willReturn($cartItem);

        $cartItem->setVariant($productVariant)->shouldBeCalled();
        $orderItemModifier->modify($cartItem, 5)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalled();

        $this->handle(new PutSimpleItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', 5));
    }
}
