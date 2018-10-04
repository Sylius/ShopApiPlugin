<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\ShopApiPlugin\Factory\AddressViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\AdjustmentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\CartItemViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\PaymentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\PlacedOrderViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ShipmentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\TotalViewFactoryInterface;
use Sylius\ShopApiPlugin\View\AdjustmentView;
use Sylius\ShopApiPlugin\View\ItemView;
use Sylius\ShopApiPlugin\View\PlacedOrderView;
use Sylius\ShopApiPlugin\View\TotalsView;

final class PlacedOrderViewFactorySpec extends ObjectBehavior
{
    public function let(
        CartItemViewFactoryInterface $cartItemViewFactory,
        AddressViewFactoryInterface $addressViewFactory,
        TotalViewFactoryInterface $totalViewFactory,
        ShipmentViewFactoryInterface $shipmentViewFactory,
        PaymentViewFactoryInterface $paymentViewFactory,
        AdjustmentViewFactoryInterface $adjustmentViewFactory
    ) {
        $this->beConstructedWith(
            $cartItemViewFactory,
            $addressViewFactory,
            $totalViewFactory,
            $shipmentViewFactory,
            $paymentViewFactory,
            $adjustmentViewFactory,
            PlacedOrderView::class
        );
    }

    function it_is_cart_factory()
    {
        $this->shouldImplement(PlacedOrderViewFactoryInterface::class);
    }

    function it_creates_a_placed_order_view(
        CartItemViewFactoryInterface $cartItemViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $orderItem,
        AdjustmentInterface $adjustment,
        AdjustmentInterface $similarAdjustment,
        AdjustmentViewFactoryInterface $adjustmentViewFactory,
        TotalViewFactoryInterface $totalViewFactory
    ) {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_COMPLETED);
        $cart->getState()->willReturn(OrderInterface::STATE_NEW);
        $cart->getTokenValue()->willReturn('ORDERTOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection([]));
        $cart->getPayments()->willReturn(new ArrayCollection([]));
        $cart
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$adjustment->getWrappedObject(), $similarAdjustment->getWrappedObject()]))
        ;

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($orderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $adjustmentView = new AdjustmentView();
        $adjustmentView->amount->current = 500;

        $adjustment->getOriginCode()->willReturn('PROMOTION_CODE');
        $adjustmentViewFactory->create($adjustment, 0, 'GBP')->willReturn($adjustmentView);

        $similarAdjustment->getOriginCode()->willReturn('PROMOTION_CODE');
        $adjustmentViewFactory->create($similarAdjustment, 500, 'GBP')->willReturn(new AdjustmentView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());

        $placedOrderView = new PlacedOrderView();
        $placedOrderView->channel = 'WEB_GB';
        $placedOrderView->currency = 'GBP';
        $placedOrderView->locale = 'en_GB';
        $placedOrderView->checkoutState = OrderCheckoutStates::STATE_COMPLETED;
        $placedOrderView->state = OrderInterface::STATE_NEW;

        $placedOrderView->items = [new ItemView()];
        $placedOrderView->totals = new TotalsView();
        $placedOrderView->cartDiscounts = ['PROMOTION_CODE' => new AdjustmentView()];

        $this->create($cart, 'en_GB')->shouldBeLike($placedOrderView);
    }
}
