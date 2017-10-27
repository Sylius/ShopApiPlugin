<?php

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\ShopApiPlugin\Command\SetDefaultAddress;
use Webmozart\Assert\Assert;

class SetDefaultAddressHandler
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository, AddressRepositoryInterface $addressRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
    }

    public function handle(SetDefaultAddress $setDefaultAddress): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressRepository->find($setDefaultAddress->id);
        /** @var CustomerInterface $customer */
        $customer = $setDefaultAddress->user->getCustomer();

        $this->assertCurrentUserIsOwner($address, $setDefaultAddress->user);

        $customer->setDefaultAddress($address);
        $this->customerRepository->add($customer);
    }

    private function assertCurrentUserIsOwner(AddressInterface $address, ShopUserInterface $user)
    {
        Assert::notNull($address->getCustomer(), 'Address is not associated with any user');
        Assert::eq($address->getCustomer()->getId(), $user->getId(), 'Current user is not owner of this address');
    }
}