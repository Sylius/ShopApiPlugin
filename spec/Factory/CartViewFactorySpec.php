<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\ShopApiPlugin\Factory\AddressViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\CartItemViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\CartViewFactory;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\View\AddressView;
use Sylius\ShopApiPlugin\View\CartSummaryView;
use Sylius\ShopApiPlugin\View\ItemView;
use Sylius\ShopApiPlugin\View\TotalsView;

class CartViewFactorySpec extends ObjectBehavior
{
    public function let(
        CartItemViewFactoryInterface $cartItemViewFactory,
        AddressViewFactoryInterface $addressViewFactory
    ) {
        $this->beConstructedWith($cartItemViewFactory, $addressViewFactory);
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
        CartItemViewFactoryInterface $cartItemViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem
    ) {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDERTOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn([$firstOrderItem, $secondOrderItem]);
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($firstOrderItem, $channel, 'en_GB')->willReturn(new ItemView());
        $cartItemViewFactory->create($secondOrderItem, $channel, 'en_GB')->willReturn(new ItemView());

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
        $cartView->items = [new ItemView(), new ItemView()];

        $this->create($cart, 'en_GB')->shouldBeLike($cartView);
    }

    function it_creates_a_cart_view_with_addresses_if_defined(
        CartItemViewFactoryInterface $cartItemViewFactory,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress,
        AddressViewFactoryInterface $addressViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem
    ) {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDERTOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn([$firstOrderItem, $secondOrderItem]);
        $cart->getShippingAddress()->willReturn($shippingAddress);
        $cart->getBillingAddress()->willReturn($billingAddress);

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($firstOrderItem, $channel, 'en_GB')->willReturn(new ItemView());
        $cartItemViewFactory->create($secondOrderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $addressViewFactory->create($shippingAddress)->willReturn(new AddressView());
        $addressViewFactory->create($billingAddress)->willReturn(new AddressView());

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDERTOKEN';
        $cartView->billingAddress = new AddressView();
        $cartView->shippingAddress = new AddressView();
        $cartView->items = [new ItemView(), new ItemView()];

        $cartView->totals = new TotalsView();
        $cartView->totals->promotion = 0;
        $cartView->totals->items = 1100;
        $cartView->totals->shipping = 500;
        $cartView->totals->taxes = 600;

        $this->create($cart, 'en_GB')->shouldBeLike($cartView);
    }
}
