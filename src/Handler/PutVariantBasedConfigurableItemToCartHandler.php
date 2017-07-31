<?php

namespace Sylius\ShopApiPlugin\Handler;

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
use Webmozart\Assert\Assert;

final class PutVariantBasedConfigurableItemToCartHandler
{
    /**
     * @var OrderRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ProductVariantRepositoryInterface
     */
    private $productVariantRepository;

    /**
     * @var CartItemFactoryInterface
     */
    private $cartItemFactory;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $orderItemModifier;

    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @param OrderRepositoryInterface $cartRepository
     * @param ProductVariantRepositoryInterface $productVariantRepository
     * @param CartItemFactoryInterface $cartItemFactory
     * @param OrderItemQuantityModifierInterface $orderItemModifier
     * @param OrderProcessorInterface $orderProcessor
     * @param ObjectManager $manager
     */
    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderProcessorInterface $orderProcessor,
        ObjectManager $manager
    ) {
        $this->cartRepository = $cartRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemModifier = $orderItemModifier;
        $this->orderProcessor = $orderProcessor;
        $this->manager = $manager;
    }

    /**
     * @param PutVariantBasedConfigurableItemToCart $putConfigurableItemToCart
     */
    public function handle(PutVariantBasedConfigurableItemToCart $putConfigurableItemToCart)
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $putConfigurableItemToCart->orderToken()]);

        Assert::notNull($cart, 'Cart has not been found');

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->productVariantRepository->findOneByCodeAndProductCode($putConfigurableItemToCart->productVariant(), $putConfigurableItemToCart->product());

        Assert::notNull($productVariant, 'Product variant has not been found');

        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->getCartItemToModify($cart, $productVariant);
        if (null !== $cartItem) {
            $this->orderItemModifier->modify($cartItem, $cartItem->getQuantity() + $putConfigurableItemToCart->quantity());
            $this->orderProcessor->process($cart);

            return;
        }

        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createForCart($cart);
        $cartItem->setVariant($productVariant);
        $this->orderItemModifier->modify($cartItem, $putConfigurableItemToCart->quantity());

        $cart->addItem($cartItem);

        $this->orderProcessor->process($cart);

        $this->manager->persist($cart);
    }

    private function getCartItemToModify(OrderInterface $cart, ProductVariantInterface $productVariant) : ?OrderItemInterface
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
