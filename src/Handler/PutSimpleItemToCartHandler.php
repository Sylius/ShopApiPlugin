<?php

namespace Sylius\ShopApiPlugin\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;

final class PutSimpleItemToCartHandler
{
    /**
     * @var OrderRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

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
     * @param ProductRepositoryInterface $productRepository
     * @param CartItemFactoryInterface $cartItemFactory
     * @param OrderItemQuantityModifierInterface $orderItemModifier
     * @param OrderProcessorInterface $orderProcessor
     * @param ObjectManager $manager
     */
    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderProcessorInterface $orderProcessor,
        ObjectManager $manager
    ) {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemModifier = $orderItemModifier;
        $this->orderProcessor = $orderProcessor;
        $this->manager = $manager;
    }

    public function handle(PutSimpleItemToCart $putSimpleItemToCart)
    {
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $putSimpleItemToCart->token()]);

        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy(['code' => $putSimpleItemToCart->product()]);

        $productVariant = $product->getVariants()[0];

        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createForCart($cart);
        $cartItem->setVariant($productVariant);
        $this->orderItemModifier->modify($cartItem, $putSimpleItemToCart->quantity());

        $cart->addItem($cartItem);

        $this->orderProcessor->process($cart);

        $this->manager->persist($cart);
    }
}
