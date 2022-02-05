<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Mapper;

use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Model\Address;
use Webmozart\Assert\Assert;

final class AddressMapper implements AddressMapperInterface
{
    /** @var FactoryInterface */
    private $addressFactory;

    /** @var RepositoryInterface */
    private $countryRepository;

    /** @var RepositoryInterface */
    private $provinceRepository;

    public function __construct(
        FactoryInterface $addressFactory,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository
    ) {
        $this->addressFactory = $addressFactory;
        $this->countryRepository = $countryRepository;
        $this->provinceRepository = $provinceRepository;
    }

    public function map(Address $addressData): AddressInterface
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();

        return $this->mapExisting($address, $addressData);
    }

    public function mapExisting(AddressInterface $address, Address $addressData): AddressInterface
    {
        $this->assertCountryExists($addressData->countryCode());

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
        } else {
            $address->setProvinceName($addressData->provinceName());
        }

        return $address;
    }

    private function assertCountryExists(string $countryCode): void
    {
        Assert::notNull($this->countryRepository->findOneBy(['code' => $countryCode]), 'Country does not exist.');
    }

    private function checkProvinceExists(string $provinceCode): ProvinceInterface
    {
        /** @var ProvinceInterface|null $province */
        $province = $this->provinceRepository->findOneBy(['code' => $provinceCode]);
        Assert::notNull($province, 'Province does not exist.');

        return $province;
    }
}
