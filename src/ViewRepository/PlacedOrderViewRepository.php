<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\PlacedOrderViewFactoryInterface;
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
        PlacedOrderViewFactoryInterface $placedOrderViewFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->placedOrderViewFactory = $placedOrderViewFactory;
    }

    public function getCompletedByCustomerEmail(string $customerEmail): array
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);

        Assert::notNull($customer);

        $cartViews = [];
        /** @var OrderInterface $order */
        foreach ($this->orderRepository->findBy(['customer' => $customer]) as $order) {
            if ($order->getCheckoutState() === OrderCheckoutStates::STATE_COMPLETED) {
                $cartViews[] = $this->placedOrderViewFactory->create($order, $order->getLocaleCode());
            }
        }

        return $cartViews;
    }
}
