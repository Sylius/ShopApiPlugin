<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Factory\AddressBook\AddressViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\AdjustmentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\CartItemViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\TotalViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\PaymentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\ShipmentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Order\PlacedOrderViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Cart\AdjustmentView;
use Sylius\ShopApiPlugin\View\Cart\TotalsView;
use Sylius\ShopApiPlugin\View\ItemView;
use Sylius\ShopApiPlugin\View\Order\PlacedOrderView;

final class PlacedOrderViewFactorySpec extends ObjectBehavior
{
    function let(
        CartItemViewFactoryInterface $cartItemViewFactory,
        AddressViewFactoryInterface $addressViewFactory,
        TotalViewFactoryInterface $totalViewFactory,
        ShipmentViewFactoryInterface $shipmentViewFactory,
        PaymentViewFactoryInterface $paymentViewFactory,
        AdjustmentViewFactoryInterface $adjustmentViewFactory
    ): void {
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

    function it_is_placed_order_view_factory(): void
    {
        $this->shouldImplement(PlacedOrderViewFactoryInterface::class);
    }

    function it_creates_a_placed_order_view_for_a_registered_user(
        CartItemViewFactoryInterface $cartItemViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $orderItem,
        AdjustmentInterface $adjustment,
        AdjustmentInterface $similarAdjustment,
        AdjustmentViewFactoryInterface $adjustmentViewFactory,
        TotalViewFactoryInterface $totalViewFactory,
        ShopUserInterface $user
    ): void {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_COMPLETED);
        $cart->getCheckoutCompletedAt()->willReturn(new \DateTime('2019-02-15T15:00:00+00:00'));
        $cart->getTokenValue()->willReturn('ORDER_TOKEN');
        $cart->getNumber()->willReturn('ORDER_NUMBER');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $cart->getShippingAddress()->shouldBeCalled()->willReturn(null);
        $cart->getBillingAddress()->shouldBeCalled()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection([]));
        $cart->getPayments()->willReturn(new ArrayCollection([]));
        $cart
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$adjustment->getWrappedObject(), $similarAdjustment->getWrappedObject()]))
        ;
        $cart->getUser()->willReturn($user);

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
        $placedOrderView->checkoutCompletedAt = '2019-02-15T15:00:00+00:00';

        $placedOrderView->items = [new ItemView()];
        $placedOrderView->totals = new TotalsView();
        $placedOrderView->cartDiscounts = ['PROMOTION_CODE' => new AdjustmentView()];
        $placedOrderView->tokenValue = 'ORDER_TOKEN';
        $placedOrderView->number = 'ORDER_NUMBER';

        $this->create($cart, 'en_GB')->shouldBeLike($placedOrderView);
    }

    function it_creates_a_placed_order_view_for_a_guest_user(
        CartItemViewFactoryInterface $cartItemViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $orderItem,
        AdjustmentInterface $adjustment,
        AdjustmentInterface $similarAdjustment,
        AdjustmentViewFactoryInterface $adjustmentViewFactory,
        TotalViewFactoryInterface $totalViewFactory
    ): void {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_COMPLETED);
        $cart->getCheckoutCompletedAt()->willReturn(new \DateTime('2019-02-15T15:00:00+00:00'));
        $cart->getTokenValue()->willReturn('ORDER_TOKEN');
        $cart->getNumber()->willReturn('ORDER_NUMBER');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $cart->getShippingAddress()->shouldNotBeCalled();
        $cart->getBillingAddress()->shouldNotBeCalled();
        $cart->getShipments()->willReturn(new ArrayCollection([]));
        $cart->getPayments()->willReturn(new ArrayCollection([]));
        $cart
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$adjustment->getWrappedObject(), $similarAdjustment->getWrappedObject()]))
        ;
        $cart->getUser()->willReturn(null);

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
        $placedOrderView->checkoutCompletedAt = '2019-02-15T15:00:00+00:00';

        $placedOrderView->items = [new ItemView()];
        $placedOrderView->totals = new TotalsView();
        $placedOrderView->cartDiscounts = ['PROMOTION_CODE' => new AdjustmentView()];
        $placedOrderView->tokenValue = 'ORDER_TOKEN';
        $placedOrderView->number = 'ORDER_NUMBER';

        $this->create($cart, 'en_GB')->shouldBeLike($placedOrderView);
    }
}
