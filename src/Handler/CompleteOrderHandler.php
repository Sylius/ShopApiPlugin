<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use SM\Factory\FactoryInterface as StateMachineFactory;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Command\CompleteOrder;
use Sylius\ShopApiPlugin\Exception\NotLoggedInException;
use Sylius\ShopApiPlugin\Provider\LoggedInUserProviderInterface;
use Webmozart\Assert\Assert;

final class CompleteOrderHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var StateMachineFactory */
    private $stateMachineFactory;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var LoggedInUserProviderInterface */
    private $loggedInUserProvider;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        LoggedInUserProviderInterface $loggedInUserProvider,
        StateMachineFactory $stateMachineFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->customerFactory = $customerFactory;
        $this->loggedInUserProvider = $loggedInUserProvider;
    }

    public function handle(CompleteOrder $completeOrder)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $completeOrder->orderToken()]);

        Assert::notNull($order, sprintf('Order with %s token has not been found.', $completeOrder->orderToken()));

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        Assert::true($stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE), sprintf('Order with %s token cannot be completed.', $completeOrder->orderToken()));

        $customer = $this->getCustomer($completeOrder->email());
        $order->setNotes($completeOrder->notes());
        $order->setCustomer($customer);

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    private function getCustomer(string $emailAddress): CustomerInterface
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $emailAddress]);

        // If the customer does not exist then it's  normal checkout
        if ($customer === null) {
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($emailAddress);

            return $customer;
        }

        // If the customer does exist the user has to be logged in with this customer. Otherwise the user is not authorized to complete the checkout
        $loggedInUser = $this->loggedInUserProvider->provide();
        if ($loggedInUser === null || $loggedInUser->getCustomer() !== $customer) {
            throw new NotLoggedInException();
        }

        return $customer;
    }
}
