<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Modifier;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Modifier\OrderModifierInterface;

final class OrderModifierSpec extends ObjectBehavior
{
    function let(
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        EntityManagerInterface $orderManager,
    ): void {
        $this->beConstructedWith($cartItemFactory, $orderItemQuantityModifier, $orderProcessor, $orderManager);
    }

    function it_is_an_order_modifier_interface(): void
    {
        $this->shouldImplement(OrderModifierInterface::class);
    }

    function it_modifies_quantity_of_existing_item_with_the_same_variant_if_it_exist(
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        EntityManagerInterface $orderManager,
        OrderInterface $order,
        OrderItemInterface $existingItem,
        ProductVariantInterface $productVariant,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$existingItem->getWrappedObject()]));

        $existingItem->getVariant()->willReturn($productVariant);
        $existingItem->getQuantity()->willReturn(3);

        $orderItemQuantityModifier->modify($existingItem, 7)->shouldBeCalled();
        $orderProcessor->process($order)->shouldBeCalled();

        $orderManager->persist(Argument::any())->shouldNotBeCalled();

        $this->modify($order, $productVariant, 4);
    }

    function it_creates_new_cart_item_and_add_it_to_order_with_proper_variant_and_quantity(
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        EntityManagerInterface $orderManager,
        OrderInterface $order,
        OrderItemInterface $cartItem,
        ProductVariantInterface $productVariant,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([]));

        $cartItemFactory->createForCart($order)->willReturn($cartItem);
        $cartItem->setVariant($productVariant)->shouldBeCalled();

        $orderItemQuantityModifier->modify($cartItem, 4)->shouldBeCalled();

        $order->addItem($cartItem)->shouldBeCalled();
        $orderProcessor->process($order)->shouldBeCalled();

        $orderManager->persist($order)->shouldBeCalled();

        $this->modify($order, $productVariant, 4);
    }

    function it_throws_an_exception_if_the_product_is_out_of_stock(
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        EntityManagerInterface $orderManager,
        OrderInterface $order,
        OrderItemInterface $cartItem,
        ProductVariantInterface $productVariant,
        AvailabilityCheckerInterface $availabilityChecker,
    ): void {
        $this->beConstructedWith($cartItemFactory, $orderItemQuantityModifier, $orderProcessor, $orderManager, $availabilityChecker);

        $order->getItems()->willReturn(new ArrayCollection([]));

        $cartItemFactory->createForCart($order)->willReturn($cartItem);

        $availabilityChecker->isStockSufficient($productVariant, 4)->shouldBeCalled()->willReturn(false);
        $orderProcessor->process(Argument::cetera())->shouldNotBeCalled();

        $orderManager->persist(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)
             ->during('modify', [$order, $productVariant, 4])
        ;
    }
}
