<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;
use Sylius\ShopApiPlugin\Provider\ProductReviewerProvider;
use Sylius\ShopApiPlugin\Provider\ProductReviewerProviderInterface;

final class ProductReviewerProviderSpec extends ObjectBehavior
{
    function let(CustomerProviderInterface $customerProvider)
    {
        $this->beConstructedWith($customerProvider);
    }

    function it_is_reviewer_subject_provider()
    {
        $this->shouldImplement(ProductReviewerProviderInterface::class);
    }

    function it_provides_product_reviewer(Customer $reviewer, CustomerProviderInterface $customerProvider)
    {
        $customerProvider->provide('example@customer.com')->willReturn($reviewer);

        $this->provide('example@customer.com')->shouldReturn($reviewer);
    }

    function it_throws_an_exception_if_customer_provider_will_not_provide_product_reviewer(
        CustomerInterface $customer,
        CustomerProviderInterface $customerProvider
    ) {
        $customerProvider->provide('example@customer.com')->willReturn($customer);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', ['example@customer.com']);
    }
}
