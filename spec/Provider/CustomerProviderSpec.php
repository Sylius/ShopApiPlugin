<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\SyliusShopApiPlugin\Provider\CustomerProviderInterface;

final class CustomerProviderSpec extends ObjectBehavior
{
    function let(CustomerRepositoryInterface $customerRepository, FactoryInterface $customerFactory)
    {
        $this->beConstructedWith($customerRepository, $customerFactory);
    }

    function it_is_customer_provider()
    {
        $this->shouldImplement(CustomerProviderInterface::class);
    }

    function it_provides_customer_from_reposiotory(CustomerRepositoryInterface $customerRepository, CustomerInterface $customer)
    {
        $customerRepository->findOneBy(['email' => 'example@customer.com'])->willReturn($customer);

        $this->provide('example@customer.com')->shouldReturn($customer);
    }

    function it_creates_new_customer_if_it_does_not_exists(
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        CustomerInterface $customer
    ) {
        $customerRepository->findOneBy(['email' => 'example@customer.com'])->willReturn(null);
        $customerFactory->createNew()->willReturn($customer);

        $customer->setEmail('example@customer.com')->shouldBeCalled();
        $customerRepository->add($customer)->shouldBeCalled();

        $this->provide('example@customer.com')->shouldReturn($customer);
    }
}
