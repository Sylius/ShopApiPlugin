<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use SM\Factory\FactoryInterface as StateMachineFactory;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Command\Cart\CompleteOrderWithCustomer;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;
use Webmozart\Assert\Assert;

final class CompleteOrderWithCustomerHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var StateMachineFactory */
    private $stateMachineFactory;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var CustomerProviderInterface */
    private $customerProvider;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        StateMachineFactory $stateMachineFactory,
        OrderProcessorInterface $orderProcessor,
        CustomerProviderInterface $customerProvider
    ) {
        $this->orderRepository = $orderRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderProcessor = $orderProcessor;
        $this->customerProvider = $customerProvider;
    }

    public function __invoke(CompleteOrderWithCustomer $completeOrderWithCustomer): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $completeOrderWithCustomer->orderToken()]);
        Assert::notNull($order, sprintf('Order with %s token has not been found.', $completeOrderWithCustomer->orderToken()));

        $this->assignCustomer($order, $completeOrderWithCustomer->email());

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
        Assert::true($stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE), sprintf('Order with %s token cannot be completed.', $completeOrderWithCustomer->orderToken()));

        $order->setNotes($completeOrderWithCustomer->notes());

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    private function assignCustomer(OrderInterface $order, string $email): void
    {
        $customer = $this->customerProvider->provide($email);

        $order->setCustomer($customer);

        $this->orderProcessor->process($order);
    }
}
