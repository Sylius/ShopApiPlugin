<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Handler;

use SM\Factory\FactoryInterface as StateMachineFactory;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\SyliusShopApiPlugin\Command\CompleteOrder;
use Sylius\SyliusShopApiPlugin\Provider\CustomerProviderInterface;
use Webmozart\Assert\Assert;

final class CompleteOrderHandler
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CustomerProviderInterface
     */
    private $customerProvider;

    /**
     * @var StateMachineFactory
     */
    private $stateMachineFactory;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerProviderInterface $customerProvider
     * @param StateMachineFactory $stateMachineFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerProviderInterface $customerProvider,
        StateMachineFactory $stateMachineFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerProvider = $customerProvider;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function handle(CompleteOrder $completeOrder)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $completeOrder->orderToken()]);

        Assert::notNull($order, sprintf('Order with %s token has not been found.', $completeOrder->orderToken()));

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        Assert::true($stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE), sprintf('Order with %s token cannot be completed.', $completeOrder->orderToken()));

        $customer = $this->customerProvider->provide($completeOrder->email());

        $order->setNotes($completeOrder->notes());
        $order->setCustomer($customer);

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }
}
