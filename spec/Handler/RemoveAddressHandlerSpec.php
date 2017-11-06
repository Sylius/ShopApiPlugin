<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\AddressRepository;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\RemoveAddress;

final class RemoveAddressHandlerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $addressRepository,
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $shopUserRepository
    ) {
        $this->beConstructedWith(
          $addressRepository,
          $orderRepository,
          $shopUserRepository
      );
    }

    function it_removes_address_from_address_book(
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressRepository $addressRepository,
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $shopUserRepository,
        AddressInterface $address
    ) {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($shopUser);
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $address->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn('USER_ID');
        $shopUser->getId()->willReturn('USER_ID');

        $orderRepository->findBy(['billingAddress' => $address])->willReturn([]);
        $orderRepository->findBy(['shippingAddress' => $address])->willReturn([]);

        $addressRepository->remove($address)->shouldBeCalled();

        $this->handle(new RemoveAddress('ADDRESS_ID', 'user@email.com'));
    }

    function it_throws_an_exception_when_deleting_address_associated_with_order(
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressRepository $addressRepository,
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $shopUserRepository,
        AddressInterface $address,
        OrderInterface $order
    ) {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($shopUser);
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $address->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn('USER_ID');
        $shopUser->getId()->willReturn('USER_ID');

        $orderRepository->findBy(['billingAddress' => $address])->willReturn($order);
        $orderRepository->findBy(['shippingAddress' => $address])->willReturn($order);

        $addressRepository->remove($address)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new RemoveAddress('ADDRESS_ID', 'user@email.com')]);
    }

    function it_trows_exception_if_address_does_not_belong_to_current_user(
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressRepository $addressRepository,
        RepositoryInterface $shopUserRepository,
        AddressInterface $address
    ) {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($shopUser);
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $address->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn('USER_ID_1');
        $shopUser->getId()->willReturn('USER_ID_2');

        $addressRepository->remove($address)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new RemoveAddress('ADDRESS_ID', 'user@email.com')]);
    }
}
