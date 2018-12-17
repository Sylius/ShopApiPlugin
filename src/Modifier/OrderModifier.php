<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Modifier;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderModifier implements OrderModifierInterface
{
    /** @var CartItemFactoryInterface */
    private $cartItemFactory;

    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var ObjectManager */
    private $orderManager;

    public function __construct(
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        ObjectManager $orderManager
    ) {
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderProcessor = $orderProcessor;
        $this->orderManager = $orderManager;
    }

    public function modify(OrderInterface $order, ProductVariantInterface $productVariant, int $quantity): void
    {
        $cartItem = $this->getCartItemToModify($order, $productVariant);
        if (null !== $cartItem) {
            $this->orderItemQuantityModifier->modify($cartItem, $cartItem->getQuantity() + $quantity);
            $this->orderProcessor->process($order);

            return;
        }

        $cartItem = $this->cartItemFactory->createForCart($order);
        $cartItem->setVariant($productVariant);
        $this->orderItemQuantityModifier->modify($cartItem, $quantity);

        $order->addItem($cartItem);

        $this->orderProcessor->process($order);

        $this->orderManager->persist($order);

        $this->orderManager->flush();
    }

    private function getCartItemToModify(OrderInterface $cart, ProductVariantInterface $productVariant): ?OrderItemInterface
    {
        /** @var OrderItemInterface $cartItem */
        foreach ($cart->getItems() as $cartItem) {
            if ($productVariant === $cartItem->getVariant()) {
                return $cartItem;
            }
        }

        return null;
    }
}
