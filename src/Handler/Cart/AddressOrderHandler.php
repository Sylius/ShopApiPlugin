<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\AddressOrder;
use Sylius\ShopApiPlugin\Mapper\AddressMapperInterface;
use Sylius\ShopApiPlugin\Model\Address;
use Webmozart\Assert\Assert;

final class AddressOrderHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var AddressMapperInterface */
    private $addressMapper;

    /** @var AddressRepositoryInterface */
    private $addressRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        AddressMapperInterface $addressMapper,
        AddressRepositoryInterface $addressRepository,
        FactoryInterface $stateMachineFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->addressMapper = $addressMapper;
        $this->addressRepository = $addressRepository;
    }

    public function __invoke(AddressOrder $addressOrder): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $addressOrder->orderToken()]);

        Assert::notNull($order, sprintf('Order with %s token has not been found.', $addressOrder->orderToken()));

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        Assert::true(
            $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS),
            sprintf('Order with %s token cannot be addressed.', $addressOrder->orderToken())
        );

        $order->setShippingAddress($this->mapAddress($order->getShippingAddress(), $addressOrder->shippingAddress()));
        $order->setBillingAddress($this->mapAddress($order->getBillingAddress(), $addressOrder->billingAddress()));

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);
    }

    public function mapAddress(?AddressInterface $original, Address $address): AddressInterface
    {
        if ($original === null) {
            $address = $this->addressMapper->map($address);
            $this->addressRepository->add($address);

            return $address;
        }

        return $this->addressMapper->mapExisting($original, $address);
    }
}
