<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\AddressBook;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\AddressBook\RemoveAddress;

final class RemoveAddressHandler
{
    /** @var AddressRepositoryInterface */
    private $addressRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(AddressRepositoryInterface $addressRepository, OrderRepositoryInterface $orderRepository)
    {
        $this->addressRepository = $addressRepository;
        $this->orderRepository = $orderRepository;
    }

    public function handle(RemoveAddress $removeAddress): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressRepository->findOneBy(['id' => $removeAddress->id()]);

        $this->addressRepository->remove($address);
    }
}
