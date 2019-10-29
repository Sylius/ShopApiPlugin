<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Order;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Order\PlacedOrderViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\PageViewFactory;
use Sylius\ShopApiPlugin\View\Order\PlacedOrderView;
use Sylius\ShopApiPlugin\View\Product\PageView;
use Webmozart\Assert\Assert;

final class PlacedOrderViewRepository implements PlacedOrderViewRepositoryInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var PlacedOrderViewFactoryInterface */
    private $placedOrderViewFactory;

    /** @var PageViewFactory */
    private $pageViewFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        PlacedOrderViewFactoryInterface $placedOrderViewFactory,
        PageViewFactory $pageViewFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->placedOrderViewFactory = $placedOrderViewFactory;
        $this->pageViewFactory         = $pageViewFactory;
    }

    public function getAllCompletedByCustomerEmail(string $customerEmail, $paginatorDetails): PageView
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);

        Assert::notNull($customer);

        $orders = $this->orderRepository->findByCustomerQuery($customer);

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($orders));

        $pagerfanta->setMaxPerPage($paginatorDetails->limit());
        $pagerfanta->setCurrentPage($paginatorDetails->page());

        $pageView =
            $this->pageViewFactory->create($pagerfanta, $paginatorDetails->route(), $paginatorDetails->parameters());

        foreach ($pagerfanta->getCurrentPageResults() as $currentPageResult) {
            $pageView->items[] = $this->placedOrderViewFactory->create($currentPageResult, $currentPageResult->getLocaleCode());
        }

        return $pageView;
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
