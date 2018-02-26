<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\SetDefaultAddress;

final class SetDefaultAddressHandlerSpec extends ObjectBehavior
{
    function let(
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository
    ) {
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
    ) {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($user);
        $addressRepository->find(1)->willReturn($address);

        $user->getCustomer()->willReturn($customer);
        $address->getCustomer()->willReturn($customer);

        $customer->getId()->willReturn('USER_ID');
        $user->getId()->willReturn('USER_ID');

        $customer->setDefaultAddress($address)->shouldBeCalled();

        $this->handle(new SetDefaultAddress(1, 'user@email.com'));
    }

    function it_trows_exception_if_address_does_not_belong_to_current_user(
        AddressInterface $address,
        AddressRepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository,
        ShopUserInterface $user,
        Customer $customer
    ) {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($user);
        $addressRepository->find(1)->willReturn($address);

        $user->getCustomer()->willReturn($customer);
        $address->getCustomer()->willReturn($customer);

        $customer->getId()->willReturn('USER_ID_1');
        $user->getId()->willReturn('USER_ID_2');

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new SetDefaultAddress(1, 'user@email.com'),
        ]);
    }

    function it_trows_exception_if_address_is_not_associated_with_any_user(
        AddressInterface $address,
        AddressRepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository,
        ShopUserInterface $user
    ) {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($user);
        $addressRepository->find(1)->willReturn($address);

        $address->getCustomer()->willReturn(null);

        $user->getId()->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new SetDefaultAddress(1, 'user@email.com'),
        ]);
    }
}
