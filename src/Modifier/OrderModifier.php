<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Modifier;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class OrderModifier implements OrderModifierInterface
{
    /** @var CartItemFactoryInterface */
    private $cartItemFactory;

    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var EntityManagerInterface */
    private $orderManager;

    /** @var AvailabilityCheckerInterface|null */
    private $availabilityChecker;

    public function __construct(
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        EntityManagerInterface $orderManager,
        ?AvailabilityCheckerInterface $availabilityChecker = null,
    ) {
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderProcessor = $orderProcessor;
        $this->orderManager = $orderManager;
        $this->availabilityChecker = $availabilityChecker;

        if ($this->availabilityChecker === null) {
            @trigger_error(sprintf('Not passing a $availabilityChecker to %s constructor is deprecated', self::class), \E_USER_DEPRECATED);
        }
    }

    public function modify(OrderInterface $order, ProductVariantInterface $productVariant, int $quantity): void
    {
        $cartItem = $this->getCartItemToModify($order, $productVariant);
        if (null !== $cartItem) {
            $targetQuantity = $cartItem->getQuantity() + $quantity;
            $this->checkCartQuantity($productVariant, $targetQuantity);

            $this->orderItemQuantityModifier->modify($cartItem, $targetQuantity);
            $this->orderProcessor->process($order);

            return;
        }

        $this->checkCartQuantity($productVariant, $quantity);
        $cartItem = $this->cartItemFactory->createForCart($order);
        $cartItem->setVariant($productVariant);
        $this->orderItemQuantityModifier->modify($cartItem, $quantity);

        $order->addItem($cartItem);

        $this->orderProcessor->process($order);

        $this->orderManager->persist($order);
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

    private function checkCartQuantity(ProductVariantInterface $productVariant, int $targetQuantity): void
    {
        if ($this->availabilityChecker === null) {
            return;
        }
        Assert::true(
            $this->availabilityChecker->isStockSufficient($productVariant, $targetQuantity),
            'Not enough stock for product variant: ' . $productVariant->getCode(),
        );
    }
}
