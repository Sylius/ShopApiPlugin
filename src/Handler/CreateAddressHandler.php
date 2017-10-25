<?php

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
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
    )
    {
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

        $this->assertShopUserExists($user);
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

        if($command->provinceName()) {
            $this->assertProvinceExists($command->provinceName());
            $address->setProvinceName($command->provinceName());
            $address->setProvinceCode($this->getProvinceCode($command->provinceName()));
        }

        $customer->addAddress($address);
        $this->addressRepository->add($address);
    }

    /**
     * @param string $countryCode
     */
    private function assertCountryExists(string $countryCode): void
    {
        Assert::notNull($this->countryRepository->findOneBy(["code" => $countryCode]), 'Country does not exist.');
    }

    /**
     * @param string $provinceName
     */
    private function assertProvinceExists(string $provinceName): void
    {
        Assert::notNull($this->provinceRepository->findOneBy(["name" => $provinceName]), 'Province does not exist.');
    }

    /**
     * @param string $provinceName
     * @return mixed
     */
    private function getProvinceCode(string $provinceName)
    {
        $province = $this->provinceRepository->findOneBy(["name" => $provinceName]);
        return $province->getCode();
    }

    /**
     * @param $user
     */
    private function assertShopUserExists($user)
    {
        Assert::isInstanceOf($user, ShopUserInterface::class, "Logged in user does not exist");
    }
}