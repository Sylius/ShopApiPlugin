<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;
use Webmozart\Assert\Assert;

final class AssignCustomerToCartHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CustomerProviderInterface */
    private $customerProvider;

    public function __construct(OrderRepositoryInterface $orderRepository, CustomerProviderInterface $customerProvider)
    {
        $this->orderRepository = $orderRepository;
        $this->customerProvider = $customerProvider;
    }

    public function __invoke(AssignCustomerToCart $assignOrderToCustomer): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $assignOrderToCustomer->orderToken()]);

        Assert::notNull($order, sprintf('Order with %s token has not been found.', $assignOrderToCustomer->orderToken()));

        $customer = $this->customerProvider->provide($assignOrderToCustomer->email());

        $order->setCustomer($customer);
    }
}
