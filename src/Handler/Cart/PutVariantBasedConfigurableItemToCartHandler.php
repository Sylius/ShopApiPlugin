<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\ShopApiPlugin\Checker\ProductInCartChannelCheckerInterface;
use Sylius\ShopApiPlugin\Command\Cart\PutVariantBasedConfigurableItemToCart;
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

    /** @var ProductInCartChannelCheckerInterface */
    private $channelChecker;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderModifierInterface $orderModifier,
        ProductInCartChannelCheckerInterface $channelChecker
    ) {
        $this->cartRepository = $cartRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->orderModifier = $orderModifier;
        $this->channelChecker = $channelChecker;
    }

    public function __invoke(PutVariantBasedConfigurableItemToCart $putConfigurableItemToCart): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $putConfigurableItemToCart->orderToken()]);
        Assert::notNull($cart, 'Cart has not been found');

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->productVariantRepository->findOneByCodeAndProductCode($putConfigurableItemToCart->productVariant(), $putConfigurableItemToCart->product());

        Assert::notNull($productVariant, 'Product variant has not been found');
        $product = $productVariant->getProduct();

        Assert::notNull($product);

        Assert::true($this->channelChecker->isProductInCartChannel($product, $cart), 'Product is not in same channel as cart');

        $this->orderModifier->modify($cart, $productVariant, $putConfigurableItemToCart->quantity());
    }
}
