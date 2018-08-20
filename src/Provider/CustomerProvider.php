<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Provider;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CustomerProvider implements CustomerProviderInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    public function __construct(CustomerRepositoryInterface $customerRepository, FactoryInterface $customerFactory)
    {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
    }

    public function provide(string $email): CustomerInterface
    {
        $customer = $this->customerRepository->findOneBy(['email' => $email]);

        if (null === $customer) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);

            $this->customerRepository->add($customer);
        }

        return $customer;
    }
}
