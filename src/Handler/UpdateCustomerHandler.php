<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\UpdateCustomer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class UpdateCustomerHandler
 */
final class UpdateCustomerHandler
{
    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UpdateAddressBookAddressHandler constructor.
     *
     * @param RepositoryInterface $customerRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        RepositoryInterface $customerRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->customerRepository = $customerRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function handle(UpdateCustomer $command): void
    {
        /** @var CustomerInterface $customer */
        $user = $this->tokenStorage->getToken()->getUser();
        $customer = $user->getCustomer();

        /** @var CustomerInterface $customer */
        $customer->setFirstName($command->firstName());
        $customer->setLastName($command->lastName());

        $customer->setEmail($command->email());
        $customer->setGender($command->gender());
        $customer->setBirthday($command->birthday());
        $customer->setPhoneNumber($command->phoneNumber());
        $customer->setSubscribedToNewsletter($command->subscribedToNewsletter());

        $this->customerRepository->add($customer);
    }
}
