<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use SM\Factory\FactoryInterface as StateMachineFactory;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\CompleteOrder;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerProviderInterface $customerProvider
     * @param StateMachineFactory $stateMachineFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerProviderInterface $customerProvider,
        StateMachineFactory $stateMachineFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerProvider = $customerProvider;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->eventDispatcher = $eventDispatcher;
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

        $this->eventDispatcher->dispatch('sylius.order.pre_complete', new ResourceControllerEvent($order));

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);

        $this->eventDispatcher->dispatch('sylius.order.post_complete', new ResourceControllerEvent($order));
    }
}
