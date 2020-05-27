<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\ShopApiPlugin\Checker\ProductInCartChannelCheckerInterface;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Modifier\OrderModifierInterface;
use Webmozart\Assert\Assert;

final class PutSimpleItemToCartHandler
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var OrderModifierInterface */
    private $orderModifier;

    /** @var ProductInCartChannelCheckerInterface */
    private $channelChecker;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        OrderModifierInterface $orderModifier,
        ProductInCartChannelCheckerInterface $channelChecker,
        AvailabilityCheckerInterface $availabilityChecker
    ) {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->orderModifier = $orderModifier;
        $this->channelChecker = $channelChecker;
        $this->availabilityChecker = $availabilityChecker;
    }

    public function __invoke(PutSimpleItemToCart $putSimpleItemToCart): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $putSimpleItemToCart->orderToken()]);
        Assert::notNull($cart, 'Cart has not been found');

        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy(['code' => $putSimpleItemToCart->product()]);
        Assert::notNull($product, 'Product has not been found');

        Assert::true($this->channelChecker->isProductInCartChannel($product, $cart), 'Product is not in same channel as cart');
        Assert::true($product->isSimple(), 'Product has to be simple');

        $productVariant = $product->getVariants()[0];

        $quantity = $putSimpleItemToCart->quantity();
        Assert::true($this->availabilityChecker->isStockSufficient($productVariant, $quantity), 'Product variant is not available in stock');

        $this->orderModifier->modify($cart, $productVariant, $quantity);
    }
}
