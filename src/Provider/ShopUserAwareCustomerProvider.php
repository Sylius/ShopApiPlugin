<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Provider;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Exception\WrongUserException;

final class ShopUserAwareCustomerProvider implements CustomerProviderInterface
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInShopUserProvider;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->loggedInShopUserProvider = $loggedInShopUserProvider;
    }

    public function provide(string $emailAddress): CustomerInterface
    {
        if ($this->loggedInShopUserProvider->isUserLoggedIn()) {
            $loggedInUser = $this->loggedInShopUserProvider->provide();

            /** @var CustomerInterface $customer */
            $customer = $loggedInUser->getCustomer();

            if ($customer->getEmail() !== $emailAddress) {
                throw new WrongUserException('Cannot finish checkout for other user, if customer is logged in.');
            }

            return $customer;
        }

        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $emailAddress]);

        if ($customer === null) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($emailAddress);

            $this->customerRepository->add($customer);

            return $customer;
        }

        if ($customer->getUser() !== null) {
            throw new WrongUserException('Customer already registered.');
        }

        return $customer;
    }
}
