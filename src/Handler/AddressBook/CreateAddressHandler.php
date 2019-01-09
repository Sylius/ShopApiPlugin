<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\AddressBook;

use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\CreateAddress;
use Webmozart\Assert\Assert;

final class CreateAddressHandler
{
    /** @var RepositoryInterface */
    private $addressRepository;

    /** @var FactoryInterface */
    private $addressFactory;

    /** @var RepositoryInterface */
    private $countryRepository;

    /** @var RepositoryInterface */
    private $provinceRepository;

    /** @var RepositoryInterface */
    private $customerRepository;

    public function __construct(
        RepositoryInterface $addressRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        RepositoryInterface $customerRepository,
        FactoryInterface $addressFactory
    ) {
        $this->addressRepository = $addressRepository;
        $this->countryRepository = $countryRepository;
        $this->provinceRepository = $provinceRepository;
        $this->addressFactory = $addressFactory;
        $this->customerRepository = $customerRepository;
    }

    public function handle(CreateAddress $command): void
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $command->userEmail()]);

        $addressData = $command->address();

        $this->assertCustomerExists($customer);
        $this->assertCountryExists($addressData->countryCode());

        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setFirstName($addressData->firstName());
        $address->setLastName($addressData->lastName());
        $address->setCompany($addressData->company());
        $address->setStreet($addressData->street());
        $address->setCountryCode($addressData->countryCode());
        $address->setCity($addressData->city());
        $address->setPostcode($addressData->postcode());
        $address->setPhoneNumber($addressData->phoneNumber());

        if (null !== $addressData->provinceCode()) {
            $province = $this->checkProvinceExists($addressData->provinceCode());
            $address->setProvinceCode($province->getCode());
            $address->setProvinceName($province->getName());
        }

        $customer->addAddress($address);

        $this->addressRepository->add($address);
    }

    private function assertCustomerExists(?CustomerInterface $customer): void
    {
        Assert::notNull($customer, 'Customer does not exists!');
    }

    private function assertCountryExists(string $countryCode): void
    {
        Assert::notNull($this->countryRepository->findOneBy(['code' => $countryCode]), 'Country does not exist.');
    }

    private function checkProvinceExists(string $provinceCode): ProvinceInterface
    {
        /** @var ProvinceInterface $province */
        $province = $this->provinceRepository->findOneBy(['code' => $provinceCode]);

        Assert::notNull($province, 'Province does not exist.');

        return $province;
    }
}
