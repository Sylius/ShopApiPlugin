<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\RemoveAddress;

final class RemoveAddressHandlerSpec extends ObjectBehavior
{
    function let(
        AddressRepositoryInterface $addressRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->beConstructedWith(
          $addressRepository,
          $orderRepository
      );
    }

    function it_removes_address_from_address_book(
        AddressRepositoryInterface $addressRepository,
        AddressInterface $address
    ) {
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $addressRepository->remove($address)->shouldBeCalled();

        $this->handle(new RemoveAddress('ADDRESS_ID', 'user@email.com'));
    }
}
