<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\ShopApiPlugin\Command\SetDefaultAddress;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class SetDefaultAddressHandler
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function handle(SetDefaultAddress $setDefaultAddress): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressRepository->find($setDefaultAddress->id());
        /** @var ShopUser */
        $user = $this->tokenStorage->getToken()->getUser();

        $this->assertCurrentUserIsOwner($address, $user);

        /** @var CustomerInterface $customer */
        $customer = $user->getCustomer();

        $customer->setDefaultAddress($address);
        $this->customerRepository->add($customer);
    }

    private function assertCurrentUserIsOwner(AddressInterface $address, ShopUserInterface $user)
    {
        Assert::notNull($address->getCustomer(), 'Address is not associated with any user');
        Assert::eq($address->getCustomer()->getId(), $user->getId(), 'Current user is not owner of this address');
    }
}
