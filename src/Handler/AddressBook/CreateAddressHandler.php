<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\AddressBook;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\AddressBook\CreateAddress;
use Sylius\ShopApiPlugin\Mapper\AddressMapperInterface;
use Webmozart\Assert\Assert;

final class CreateAddressHandler
{
    /** @var RepositoryInterface */
    private $addressRepository;

    /** @var RepositoryInterface */
    private $customerRepository;

    /** @var AddressMapperInterface */
    private $addressMapper;

    public function __construct(
        RepositoryInterface $addressRepository,
        RepositoryInterface $customerRepository,
        AddressMapperInterface $addressMapper
    ) {
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
        $this->addressMapper = $addressMapper;
    }

    public function __invoke(CreateAddress $command): void
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $command->userEmail()]);
        Assert::notNull($customer);

        $address = $this->addressMapper->map($command->address());

        $customer->addAddress($address);
        $this->addressRepository->add($address);
    }

}
