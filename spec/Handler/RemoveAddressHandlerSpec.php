<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\AddressRepository;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\RemoveAddress;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class RemoveAddressHandlerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $addressRepository,
        OrderRepositoryInterface $orderRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->beConstructedWith(
          $addressRepository,
          $orderRepository,
          $tokenStorage
      );
    }

    function it_removes_address_from_address_book(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressRepository $addressRepository,
        OrderRepositoryInterface $orderRepository,
        AddressInterface $address
    ) {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $customer->getId()->willReturn('USER_ID');
        $shopUser->getId()->willReturn('USER_ID');
        $address->getCustomer()->willReturn($customer);

        $orderRepository->findBy(['billingAddress' => $address])->willReturn([]);
        $orderRepository->findBy(['shippingAddress' => $address])->willReturn([]);

        $addressRepository->remove($address)->shouldBeCalled();

        $this->handle(new RemoveAddress('ADDRESS_ID'));
    }

    function it_throws_an_exception_when_deleting_address_associated_with_order(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressRepository $addressRepository,
        OrderRepositoryInterface $orderRepository,
        AddressInterface $address,
        OrderInterface $order
    ) {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $customer->getId()->willReturn('USER_ID');
        $shopUser->getId()->willReturn('USER_ID');
        $address->getCustomer()->willReturn($customer);

        $orderRepository->findBy(['billingAddress' => $address])->willReturn($order);
        $orderRepository->findBy(['shippingAddress' => $address])->willReturn($order);

        $addressRepository->remove($address)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new RemoveAddress('ADDRESS_ID')]);
    }

    function it_trows_exception_if_address_does_not_belong_to_current_user(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressRepository $addressRepository,
        AddressInterface $address
    ) {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $customer->getId()->willReturn('USER_ID_1');
        $shopUser->getId()->willReturn('USER_ID_2');
        $address->getCustomer()->willReturn($customer);

        $addressRepository->remove($address)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new RemoveAddress('ADDRESS_ID')]);
    }
}
