<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\ShopApiPlugin\Factory\CartViewFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\Factory\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\CartSummaryView;
use Sylius\ShopApiPlugin\View\ItemView;
use Sylius\ShopApiPlugin\View\ProductVariantView;
use Sylius\ShopApiPlugin\View\ProductView;
use Sylius\ShopApiPlugin\View\TotalsView;

class CartViewFactorySpec extends ObjectBehavior
{
    public function let(
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantViewFactoryInterface $productVariantViewFactory
    ) {
        $this->beConstructedWith($productViewFactory, $productVariantViewFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartViewFactory::class);
    }

    function it_is_cart_factory_interface()
    {
        $this->shouldHaveType(CartViewFactory::class);
    }

    function it_creates_a_cart_view(
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductInterface $tshirt,
        ProductInterface $mug,
        ProductVariantInterface $tshirtVariant,
        ProductVariantInterface $mugVariant,
        ProductVariantViewFactoryInterface $productVariantViewFactory,
        ProductViewFactoryInterface $productViewFactory
    ) {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDERTOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn([$firstOrderItem, $secondOrderItem]);

        $channel->getCode()->willReturn('WEB_GB');

        $firstOrderItem->getId()->willReturn(2);
        $firstOrderItem->getQuantity()->willReturn(3);
        $firstOrderItem->getTotal()->willReturn(900);
        $firstOrderItem->getProduct()->willReturn($tshirt);
        $firstOrderItem->getVariant()->willReturn($tshirtVariant);

        $secondOrderItem->getId()->willReturn(5);
        $secondOrderItem->getQuantity()->willReturn(1);
        $secondOrderItem->getTotal()->willReturn(200);
        $secondOrderItem->getProduct()->willReturn($mug);
        $secondOrderItem->getVariant()->willReturn($mugVariant);

        $productViewFactory->create($tshirt, 'en_GB')->willReturn(new ProductView());
        $productViewFactory->create($mug, 'en_GB')->willReturn(new ProductView());

        $productVariantViewFactory->create($tshirtVariant, $channel, 'en_GB')->willReturn(new ProductVariantView());
        $productVariantViewFactory->create($mugVariant, $channel, 'en_GB')->willReturn(new ProductVariantView());

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDERTOKEN';

        $cartView->totals = new TotalsView();
        $cartView->totals->promotion = 0;
        $cartView->totals->items = 1100;
        $cartView->totals->shipping = 500;
        $cartView->totals->taxes = 600;

        $firstOrderItemView = new ItemView();
        $firstOrderItemView->id = 2;
        $firstOrderItemView->quantity = 3;
        $firstOrderItemView->total = 900;
        $firstOrderItemView->product = new ProductView();
        $firstOrderItemView->product->variants = [new ProductVariantView()];

        $secondOrderItemView = new ItemView();
        $secondOrderItemView->id = 5;
        $secondOrderItemView->quantity = 1;
        $secondOrderItemView->total = 200;
        $secondOrderItemView->product = new ProductView();
        $secondOrderItemView->product->variants = [new ProductVariantView()];

        $this->create($cart, 'en_GB');
    }
}