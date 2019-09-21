<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use SM\Factory\FactoryInterface as StateMachineFactory;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\CompleteOrder;
use Webmozart\Assert\Assert;

final class CompleteOrderHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var StateMachineFactory */
    private $stateMachineFactory;

    public function __construct(OrderRepositoryInterface $orderRepository, StateMachineFactory $stateMachineFactory)
    {
        $this->orderRepository = $orderRepository;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function __invoke(CompleteOrder $completeOrder): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $completeOrder->orderToken()]);

        Assert::notNull($order, sprintf('Order with %s token has not been found.', $completeOrder->orderToken()));

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        Assert::true($stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE), sprintf('Order with %s token cannot be completed.', $completeOrder->orderToken()));

        $order->setNotes($completeOrder->notes());

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }
}
