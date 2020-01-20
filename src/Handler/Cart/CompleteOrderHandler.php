<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use SM\Factory\FactoryInterface as StateMachineFactory;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\CompleteOrder;
use Webmozart\Assert\Assert;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;


final class CompleteOrderHandler
{

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var StateMachineFactory */
    private $stateMachineFactory;
    /**
     * @var FactoryInterface
     */
    private $adjustmentFactory;
    /**
     * @var OrderProcessorInterface
     */private $orderProcessor;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        StateMachineFactory $stateMachineFactory,
        FactoryInterface $adjustmentFactory,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->orderRepository     = $orderRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->adjustmentFactory   = $adjustmentFactory;
        $this->orderProcessor   = $orderProcessor;
    }

    public function __invoke(CompleteOrder $completeOrder): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $completeOrder->orderToken()]);

        Assert::notNull($order, sprintf('Order with %s token has not been found.', $completeOrder->orderToken()));

        $order->setNotes($completeOrder->notes());
        if($completeOrder->points()){
            $points = $this->createAdjustment($completeOrder->points()*100);
            $order->addAdjustment($points);
        }
        $this->orderProcessor->process($order);

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        Assert::true($stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE), sprintf('Order with %s token cannot be completed.', $completeOrder->orderToken()));

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    public function createAdjustment($amount)
    {
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType('points_discount');
        $adjustment->setLabel('E_ball');
        $adjustment->setOriginCode('E_ball');
        $adjustment->setAmount(-($amount));

        return $adjustment;
    }

}
