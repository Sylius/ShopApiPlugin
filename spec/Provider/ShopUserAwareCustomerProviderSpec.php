<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Exception\WrongUserException;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;

final class ShopUserAwareCustomerProviderSpec extends ObjectBehavior
{
    function let(
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
    ): void {
        $this->beConstructedWith($customerRepository, $customerFactory, $loggedInShopUserProvider);
    }

    function it_is_customer_provider(): void
    {
        $this->shouldImplement(CustomerProviderInterface::class);
    }

    function it_provides_customer_from_reposiotory_if_it_does_not_have_related_shop_user(
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
    ): void {
        $loggedInShopUserProvider->isUserLoggedIn()->willReturn(false);

        $customerRepository->findOneBy(['email' => 'example@customer.com'])->willReturn($customer);

        $customer->getUser()->willReturn(null);

        $this->provide('example@customer.com')->shouldReturn($customer);
    }

    function it_creates_new_customer_if_it_does_not_exists(
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        CustomerInterface $customer,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
    ): void {
        $loggedInShopUserProvider->isUserLoggedIn()->willReturn(false);
        $customerRepository->findOneBy(['email' => 'example@customer.com'])->willReturn(null);
        $customerFactory->createNew()->willReturn($customer);

        $customer->setEmail('example@customer.com')->shouldBeCalled();
        $customerRepository->add($customer)->shouldBeCalled();

        $this->provide('example@customer.com')->shouldReturn($customer);
    }

    function it_provides_customer_from_reposiotory_if_it_has_related_shop_user_and_user_is_logged_in(
        CustomerInterface $customer,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
        ShopUserInterface $shopUser,
    ): void {
        $loggedInShopUserProvider->isUserLoggedIn()->willReturn(true);
        $loggedInShopUserProvider->provide()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('example@customer.com');

        $this->provide('example@customer.com')->shouldReturn($customer);
    }

    function it_throws_an_exception_if_requested_customer_is_not_logged_in_but_has_related_shop_user(
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
        ShopUserInterface $shopUser,
    ): void {
        $customerRepository->findOneBy(['email' => 'example@customer.com'])->willReturn($customer);
        $loggedInShopUserProvider->isUserLoggedIn()->willReturn(false);

        $customer->getUser()->willReturn($shopUser);

        $this->shouldThrow(WrongUserException::class)->during('provide', ['example@customer.com']);
    }

    function it_throws_an_exception_if_requested_customer_is_logged_in_but_customer_is_related_to_another_shop_user(
        CustomerInterface $customer,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
        ShopUserInterface $shopUser,
    ): void {
        $loggedInShopUserProvider->isUserLoggedIn()->willReturn(true);
        $loggedInShopUserProvider->provide()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('anotherCustomer@customer.com');

        $this->shouldThrow(WrongUserException::class)->during('provide', ['example@customer.com']);
    }
}
