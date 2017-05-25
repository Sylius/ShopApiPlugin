<?php

namespace Sylius\ShopApiPlugin\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\ShopApiPlugin\Command\PutOptionBasedConfigurableItemToCart;
use Webmozart\Assert\Assert;

final class PutOptionBasedConfigurableItemToCartHandler
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

    /**
     * @param PutOptionBasedConfigurableItemToCart $putConfigurableItemToCart
     */
    public function handle(PutOptionBasedConfigurableItemToCart $putConfigurableItemToCart)
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $putConfigurableItemToCart->orderToken()]);

        Assert::notNull($cart, 'Cart has not been found');

        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneByCode($putConfigurableItemToCart->product());

        Assert::notNull($product, 'Product has not been found');

        $productVariant = $this->getVariant($putConfigurableItemToCart->options(), $product);

        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createForCart($cart);
        $cartItem->setVariant($productVariant);
        $this->orderItemModifier->modify($cartItem, $putConfigurableItemToCart->quantity());

        $cart->addItem($cartItem);

        $this->orderProcessor->process($cart);

        $this->manager->persist($cart);
    }

    /**
     * @param array $options
     * @param ProductInterface $product
     *
     * @return null|ProductVariantInterface
     */
    private function getVariant(array $options, ProductInterface $product)
    {
        foreach ($product->getVariants() as $variant) {
            if ($this->areOptionsMatched($options, $variant)) {
                return $variant;
            }
        }

        throw new \InvalidArgumentException('Variant could not be resolved');
    }

    /**
     * @param array $options
     * @param ProductVariantInterface $variant
     *
     * @return bool
     */
    private function areOptionsMatched(array $options, ProductVariantInterface $variant)
    {
        foreach ($variant->getOptionValues() as $optionValue) {
            if (!isset($options[$optionValue->getOptionCode()]) || $optionValue->getCode() !== $options[$optionValue->getOptionCode()]) {
                return false;
            }
        }

        return true;
    }
}
