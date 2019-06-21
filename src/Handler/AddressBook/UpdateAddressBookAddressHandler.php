<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\AddressBook;

use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\AddressBook\UpdateAddress;
use Sylius\ShopApiPlugin\Mapper\AddressMapperInterface;
use Webmozart\Assert\Assert;

final class UpdateAddressBookAddressHandler
{
    /** @var RepositoryInterface */
    private $addressRepository;

    /** @var RepositoryInterface */
    private $shopUserRepository;

    /** @var AddressMapperInterface */
    private $addressMapper;

    public function __construct(
        RepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository,
        AddressMapperInterface $addressMapper
    ) {
        $this->addressRepository = $addressRepository;
        $this->shopUserRepository = $shopUserRepository;
        $this->addressMapper = $addressMapper;
    }

    public function __invoke(UpdateAddress $command): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressRepository->findOneBy(['id' => $command->id()]);
        Assert::notNull($address, 'Address does not exist');

        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->shopUserRepository->findOneBy(['username' => $command->userEmail()]);

        $this->assertCurrentUserIsOwner($address, $shopUser);

        $address = $this->addressMapper->mapExisting($address, $command->address());

        $this->addressRepository->add($address);
    }

    private function assertCurrentUserIsOwner(AddressInterface $address, ShopUserInterface $user)
    {
        Assert::notNull($address->getCustomer(), 'Address is not associated with any user');
        Assert::eq($address->getCustomer(), $user->getCustomer(), 'Current user is not owner of this address');
    }

}
