<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
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
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $putConfigurableItemToCart->orderToken()]);

        Assert::notNull($cart, 'Cart has not been found');

        /** @var ProductInterface $product */
        $productVariant = $this->productVariantRepository->findOneByCodeAndProductCode($putConfigurableItemToCart->productVariant(), $putConfigurableItemToCart->product());

        Assert::notNull($productVariant, 'Product variant has not been found');

        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createForCart($cart);
        $cartItem->setVariant($productVariant);
        $this->orderItemModifier->modify($cartItem, $putConfigurableItemToCart->quantity());

        $cart->addItem($cartItem);

        $this->orderProcessor->process($cart);

        $this->manager->persist($cart);
    }
}
