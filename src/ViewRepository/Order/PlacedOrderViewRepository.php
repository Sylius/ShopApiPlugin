<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Order;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Order\PlacedOrderViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Order\PlacedOrderView;
use Webmozart\Assert\Assert;

final class PlacedOrderViewRepository implements PlacedOrderViewRepositoryInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var PlacedOrderViewFactoryInterface */
    private $placedOrderViewFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        PlacedOrderViewFactoryInterface $placedOrderViewFactory,
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->placedOrderViewFactory = $placedOrderViewFactory;
    }

    public function getAllCompletedByCustomerEmail(string $customerEmail): array
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);

        Assert::notNull($customer);

        $cartViews = [];

        foreach ($this->orderRepository->findByCustomer($customer) as $order) {
            /** @var OrderInterface $order */
            $cartViews[] = $this->placedOrderViewFactory->create($order, $order->getLocaleCode());
        }

        return $cartViews;
    }

    public function getOneCompletedByCustomerEmailAndToken(string $customerEmail, string $tokenValue): PlacedOrderView
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);

        Assert::notNull($customer);

        /** @var OrderInterface|null $order */
        $order = $this
            ->orderRepository
            ->findOneBy(['tokenValue' => $tokenValue, 'customer' => $customer, 'checkoutState' => OrderCheckoutStates::STATE_COMPLETED])
        ;

        Assert::notNull($order, sprintf('There is no placed order with with token %s for customer with email %s', $tokenValue, $customerEmail));

        return $this->placedOrderViewFactory->create($order, $order->getLocaleCode());
    }

    public function getOneCompletedByGuestAndToken(string $tokenValue): PlacedOrderView
    {
        /** @var OrderInterface|null $order */
        $order = $this
            ->orderRepository
            ->findOneBy(['tokenValue' => $tokenValue, 'checkoutState' => OrderCheckoutStates::STATE_COMPLETED])
        ;

        Assert::notNull($order, sprintf('There is no placed order with with token %s', $tokenValue));
        Assert::null($order->getUser(), sprintf('Order with token %s placed by a registered user', $tokenValue));

        return $this->placedOrderViewFactory->create($order, $order->getLocaleCode());
    }
}
