<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Customer;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\Customer\UpdateCustomer;

final class UpdateCustomerHandler
{
    /** @var RepositoryInterface */
    private $customerRepository;

    public function __construct(
        RepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    public function __invoke(UpdateCustomer $command): void
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $command->email()]);

        $customer->setFirstName($command->firstName());
        $customer->setLastName($command->lastName());
        $customer->setGender($command->gender());
        $customer->setBirthday($command->birthday());
        $customer->setPhoneNumber($command->phoneNumber());
        $customer->setSubscribedToNewsletter($command->subscribedToNewsletter());

        $this->customerRepository->add($customer);
    }
}
