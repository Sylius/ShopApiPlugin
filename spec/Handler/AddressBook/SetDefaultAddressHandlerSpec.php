<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\AddressBook;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\AddressBook\SetDefaultAddress;

final class SetDefaultAddressHandlerSpec extends ObjectBehavior
{
    function let(
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository
    ): void {
        $this->beConstructedWith(
            $customerRepository,
            $addressRepository,
            $shopUserRepository
        );
    }

    function it_handles_setting_default_address_for_user(
        AddressInterface $address,
        AddressRepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository,
        ShopUserInterface $user,
        Customer $customer
    ): void {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($user);
        $addressRepository->find(1)->willReturn($address);

        $user->getCustomer()->willReturn($customer);
        $address->getCustomer()->willReturn($customer);

        $customer->getId()->willReturn('CUSTOMER_ID');

        $customer->setDefaultAddress($address)->shouldBeCalled();

        $this->handle(new SetDefaultAddress(1, 'user@email.com'));
    }

    function it_throws_exception_if_address_does_not_belong_to_current_user(
        AddressInterface $address,
        AddressRepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository,
        ShopUserInterface $user,
        CustomerInterface $customer,
        CustomerInterface $anotherCustomer
    ): void {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($user);
        $addressRepository->find(1)->willReturn($address);

        $user->getCustomer()->willReturn($customer);
        $address->getCustomer()->willReturn($anotherCustomer);

        $customer->getId()->willReturn('CUSTOMER_ID');
        $anotherCustomer->getId()->willReturn('ANOTHER_CUSTOMER_ID');

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new SetDefaultAddress(1, 'user@email.com'),
        ]);
    }

    function it_throws_exception_if_address_is_not_associated_with_any_user(
        AddressInterface $address,
        AddressRepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository,
        ShopUserInterface $user
    ): void {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($user);
        $addressRepository->find(1)->willReturn($address);

        $address->getCustomer()->willReturn(null);

        $user->getCustomer()->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new SetDefaultAddress(1, 'user@email.com'),
        ]);
    }
}
