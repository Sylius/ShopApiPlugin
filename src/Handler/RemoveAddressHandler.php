<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\RemoveAddress;
use Webmozart\Assert\Assert;

final class RemoveAddressHandler
{
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(AddressRepositoryInterface $addressRepository, OrderRepositoryInterface $orderRepository)
    {
        $this->addressRepository = $addressRepository;
        $this->orderRepository = $orderRepository;
    }

    public function handle(RemoveAddress $removeAddress)
    {
        /** @var AddressInterface $address */
        $address = $this->addressRepository->findOneBy(['id' => $removeAddress->id]);

        $this->assertCurrentUserIsOwner($address, $removeAddress->user);
        $this->assertOrderWithAddressNotExists($address);

        $this->addressRepository->remove($address);
    }

    private function assertOrderWithAddressNotExists($address)
    {
        /** @var OrderInterface $orderShippingAddress */
        $orderShippingAddress = $this->orderRepository->findBy(['billingAddress' => $address]);
        /** @var OrderInterface $orderBillingAddress */
        $orderBillingAddress = $this->orderRepository->findBy(['shippingAddress' => $address]);
        Assert::allIsEmpty([$orderShippingAddress, $orderBillingAddress], 'Cant delete address because it is associated with one or more orders');
    }

    private function assertCurrentUserIsOwner(AddressInterface $address, ShopUserInterface $user)
    {
        Assert::eq($address->getCustomer()->getId(), $user->getId(), 'User is not owner of this address');
    }
}
