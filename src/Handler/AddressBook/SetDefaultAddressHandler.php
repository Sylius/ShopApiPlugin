<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\AddressBook;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\SetDefaultAddress;
use Webmozart\Assert\Assert;

final class SetDefaultAddressHandler
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var AddressRepositoryInterface */
    private $addressRepository;

    /** @var RepositoryInterface */
    private $shopUserRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->shopUserRepository = $shopUserRepository;
    }

    public function handle(SetDefaultAddress $setDefaultAddress): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressRepository->find($setDefaultAddress->id());
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->shopUserRepository->findOneBy(['username' => $setDefaultAddress->userEmail()]);

        $this->assertCurrentUserIsOwner($address, $shopUser);

        /** @var CustomerInterface $customer */
        $customer = $shopUser->getCustomer();

        $customer->setDefaultAddress($address);
        $this->customerRepository->add($customer);
    }

    private function assertCurrentUserIsOwner(AddressInterface $address, ShopUserInterface $user)
    {
        Assert::notNull($address->getCustomer(), 'Address is not associated with any user.');
        Assert::eq($address->getCustomer()->getId(), $user->getCustomer()->getId(), 'Current user is not owner of this address.');
    }
}
