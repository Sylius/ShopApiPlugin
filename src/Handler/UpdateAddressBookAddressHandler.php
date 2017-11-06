<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\UpdateAddress;
use Webmozart\Assert\Assert;

final class UpdateAddressBookAddressHandler
{
    /**
     * @var RepositoryInterface
     */
    private $addressRepository;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @var RepositoryInterface
     */
    private $provinceRepository;

    /**
     * @var RepositoryInterface
     */
    private $shopUserRepository;

    /**
     * @param RepositoryInterface $addressRepository
     * @param RepositoryInterface $countryRepository
     * @param RepositoryInterface $provinceRepository
     * @param RepositoryInterface $shopUserRepository
     * @param FactoryInterface $addressFactory
     */
    public function __construct(
        RepositoryInterface $addressRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        RepositoryInterface $shopUserRepository,
        FactoryInterface $addressFactory
    ) {
        $this->addressRepository = $addressRepository;
        $this->countryRepository = $countryRepository;
        $this->provinceRepository = $provinceRepository;
        $this->addressFactory = $addressFactory;
        $this->shopUserRepository = $shopUserRepository;
    }

    public function handle(UpdateAddress $command): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressRepository->findOneBy(['id' => $command->id()]);
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->shopUserRepository->findOneBy(['username' => $command->userEmail()]);

        $this->assertAddressExists($address);
        $this->assertCurrentUserIsOwner($address, $shopUser);
        $this->assertCountryExists($command->countryCode());

        /** @var AddressInterface $address */
        $address->setFirstName($command->firstName());
        $address->setLastName($command->lastName());
        $address->setCompany($command->company());
        $address->setStreet($command->street());
        $address->setCountryCode($command->countryCode());
        $address->setCity($command->city());
        $address->setPostcode($command->postcode());
        $address->setPhoneNumber($command->phoneNumber());

        if (null !== $command->provinceCode() && $command->provinceCode() !== $address->getProvinceCode()) {
            $province = $this->provinceRepository->findOneBy(['code' => $command->provinceCode()]);
            $this->assertProvinceExists($province);
            $address->setProvinceCode($province->getCode());
            $address->setProvinceName($province->getName());
        }

        $this->addressRepository->add($address);
    }

    private function assertCountryExists(string $countryCode): void
    {
        Assert::notNull($this->countryRepository->findOneBy(['code' => $countryCode]), 'Country does not exist.');
    }

    private function assertProvinceExists($province): void
    {
        Assert::notNull($province, 'Province does not exist.');
    }

    private function assertCurrentUserIsOwner(AddressInterface $address, ShopUserInterface $user)
    {
        Assert::notNull($address->getCustomer(), 'Address is not associated with any user');
        Assert::eq($address->getCustomer(), $user->getCustomer(), 'Current user is not owner of this address');
    }

    private function assertAddressExists($address)
    {
        Assert::notNull($address, 'Address does not exist!');
    }
}
