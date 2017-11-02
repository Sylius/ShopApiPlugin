<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\CreateAddress;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class CreateAddressHandler
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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * CreateAddressHandler constructor.
     *
     * @param RepositoryInterface $addressRepository
     * @param RepositoryInterface $countryRepository
     * @param RepositoryInterface $provinceRepository
     * @param FactoryInterface $addressFactory
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        RepositoryInterface $addressRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        FactoryInterface $addressFactory,
        TokenStorageInterface $tokenStorage
    ) {
        $this->addressRepository = $addressRepository;
        $this->countryRepository = $countryRepository;
        $this->provinceRepository = $provinceRepository;
        $this->addressFactory = $addressFactory;
        $this->tokenStorage = $tokenStorage;
    }

    public function handle(CreateAddress $command): void
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $customer = $user->getCustomer();

        $this->assertCountryExists($command->countryCode());

        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setFirstName($command->firstName());
        $address->setLastName($command->lastName());
        $address->setCompany($command->company());
        $address->setStreet($command->street());
        $address->setCountryCode($command->countryCode());
        $address->setCity($command->city());
        $address->setPostcode($command->postcode());
        $address->setPhoneNumber($command->phoneNumber());

        if (null !== $command->provinceCode()) {
            $province = $this->provinceRepository->findOneBy(['code' => $command->provinceCode()]);
            $this->assertProvinceExists($province);
            $address->setProvinceCode($province->getCode());
            $address->setProvinceName($province->getName());
        }

        $customer->addAddress($address);
        $this->addressRepository->add($address);
    }

    /**
     * @param string $countryCode
     */
    private function assertCountryExists(string $countryCode): void
    {
        Assert::notNull($this->countryRepository->findOneBy(['code' => $countryCode]), 'Country does not exist.');
    }

    /**
     * @param $province
     */
    private function assertProvinceExists($province): void
    {
        Assert::notNull($province, 'Province does not exist.');
    }
}
