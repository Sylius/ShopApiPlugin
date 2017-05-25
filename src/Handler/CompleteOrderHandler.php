<?php

namespace Sylius\ShopApiPlugin\Handler;

use SM\Factory\FactoryInterface as StateMachineFactory;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Command\CompleteOrder;
use Webmozart\Assert\Assert;

final class CompleteOrderHandler
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var StateMachineFactory
     */
    private $stateMachineFactory;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param FactoryInterface $customerFactory
     * @param StateMachineFactory $stateMachineFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        StateMachineFactory $stateMachineFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function handle(CompleteOrder $completeOrder)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $completeOrder->orderToken()]);

        Assert::notNull($order, sprintf('Order with %s token has not been found.', $completeOrder->orderToken()));

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        Assert::true($stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE), sprintf('Order with %s token cannot be completed.', $completeOrder->orderToken()));

        $customer = $this->provideCustometer($completeOrder);

        $order->setNotes($completeOrder->notes());
        $order->setCustomer($customer);

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    /**
     * @param CompleteOrder $completeOrder
     *
     * @return CustomerInterface
     */
    private function provideCustometer(CompleteOrder $completeOrder)
    {
        $customer =  $this->customerRepository->findOneBy(['email' => $completeOrder->email()]);

        if (null === $customer) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($completeOrder->email());

            $this->customerRepository->add($customer);
        }

        return $customer;
    }
}
