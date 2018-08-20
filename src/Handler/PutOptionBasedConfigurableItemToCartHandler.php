<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Handler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\SyliusShopApiPlugin\Command\PutOptionBasedConfigurableItemToCart;
use Sylius\SyliusShopApiPlugin\Modifier\OrderModifierInterface;
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
     * @var OrderModifierInterface
     */
    private $orderModifier;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        OrderModifierInterface $orderModifier
    ) {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->orderModifier = $orderModifier;
    }

    public function handle(PutOptionBasedConfigurableItemToCart $putConfigurableItemToCart)
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $putConfigurableItemToCart->orderToken()]);

        Assert::notNull($cart, 'Cart has not been found');

        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneByCode($putConfigurableItemToCart->product());

        Assert::notNull($product, 'Product has not been found');

        $productVariant = $this->getVariant($putConfigurableItemToCart->options(), $product);

        $this->orderModifier->modify($cart, $productVariant, $putConfigurableItemToCart->quantity());
    }

    /**
     * @param array $options
     * @param ProductInterface $product
     *
     * @return ProductVariantInterface|null
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
