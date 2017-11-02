<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\ShopApiPlugin\Command\SetDefaultAddress;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class SetDefaultAddressHandlerSpec extends ObjectBehavior
{
    function let(
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->beConstructedWith($customerRepository, $addressRepository, $tokenStorage);
    }

    function it_handles_setting_default_address_for_user(
        AddressInterface $address,
        AddressRepositoryInterface $addressRepository,
        TokenStorageInterface $tokenStorage,
        ShopUserInterface $user,
        Customer $customer,
        JWTUserToken $userToken
    ) {
        $addressRepository->find('ADDRESS_ID')->willReturn($address);

        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $address->getCustomer()->willReturn($customer);

        $customer->getId()->willReturn('USER_ID');
        $user->getId()->willReturn('USER_ID');

        $customer->setDefaultAddress($address)->shouldBeCalled();

        $this->handle(new SetDefaultAddress('ADDRESS_ID'));
    }

    function it_trows_exception_if_address_does_not_belong_to_current_user(
        AddressInterface $address,
        AddressRepositoryInterface $addressRepository,
        TokenStorageInterface $tokenStorage,
        ShopUserInterface $user,
        Customer $customer,
        JWTUserToken $userToken
    ) {
        $addressRepository->find('ADDRESS_ID')->willReturn($address);

        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $address->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn('USER_ID_1');
        $user->getId()->willReturn('USER_ID_2');

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new SetDefaultAddress('ADDRESS_ID')]);
    }

    function it_trows_exception_if_address_is_not_associated_with_any_user(
        AddressInterface $address,
        AddressRepositoryInterface $addressRepository,
        TokenStorageInterface $tokenStorage,
        ShopUserInterface $user,
        JWTUserToken $userToken
    ) {
        $addressRepository->find('ADDRESS_ID')->willReturn($address);
        $address->getCustomer()->willReturn(null);

        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($user);

        $user->getId()->shouldNotBeCalled();
        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new SetDefaultAddress('ADDRESS_ID')]);
    }
}
