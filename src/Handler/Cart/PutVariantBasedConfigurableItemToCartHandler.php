<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\ShopApiPlugin\Command\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Modifier\OrderModifierInterface;
use Webmozart\Assert\Assert;

final class PutVariantBasedConfigurableItemToCartHandler
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var OrderModifierInterface */
    private $orderModifier;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderModifierInterface $orderModifier
    ) {
        $this->cartRepository = $cartRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->orderModifier = $orderModifier;
    }

    public function handle(PutVariantBasedConfigurableItemToCart $putConfigurableItemToCart)
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $putConfigurableItemToCart->orderToken()]);

        Assert::notNull($cart, 'Cart has not been found');

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->productVariantRepository->findOneByCodeAndProductCode($putConfigurableItemToCart->productVariant(), $putConfigurableItemToCart->product());

        Assert::notNull($productVariant, 'Product variant has not been found');

        $this->orderModifier->modify($cart, $productVariant, $putConfigurableItemToCart->quantity());
    }
}
