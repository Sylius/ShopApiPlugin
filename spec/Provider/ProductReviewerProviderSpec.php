<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Customer;
use Sylius\SyliusShopApiPlugin\Provider\CustomerProviderInterface;
use Sylius\SyliusShopApiPlugin\Provider\ProductReviewerProviderInterface;

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
}
