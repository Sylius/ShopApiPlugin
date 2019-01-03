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
use Sylius\ShopApiPlugin\Exception\WrongUserException;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
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

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInUserProvider;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        LoggedInShopUserProviderInterface $loggedInUserProvider,
        StateMachineFactory $stateMachineFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->customerFactory = $customerFactory;
        $this->loggedInUserProvider = $loggedInUserProvider;
    }

    public function handle(CompleteOrder $completeOrder): void
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
        try {
            $loggedInUser = $this->loggedInUserProvider->provide();

            if ($emailAddress !== '') {
                throw new \InvalidArgumentException($emailAddress . ' has to be empty');

                throw new \InvalidArgumentException('Can not have a logged in user and an email address');
            }

            /** @var CustomerInterface $customer */
            $customer = $loggedInUser->getCustomer();

            return $customer;
        } catch (TokenNotFoundException $notLoggedIn) {
            /** @var CustomerInterface|null $customer */
            $customer = $this->customerRepository->findOneBy(['email' => $emailAddress]);

            // If the customer does not exist then it's  normal checkout
            if ($customer === null) {
                /** @var CustomerInterface $customer */
                $customer = $this->customerFactory->createNew();
                $customer->setEmail($emailAddress);

                return $customer;
            }

            throw new WrongUserException('Email is already taken');
        }
    }
}
