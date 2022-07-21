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
use Sylius\Component\Core\Model\Customer;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;
use Sylius\ShopApiPlugin\Provider\ProductReviewerProviderInterface;

final class ProductReviewerProviderSpec extends ObjectBehavior
{
    function let(CustomerProviderInterface $customerProvider): void
    {
        $this->beConstructedWith($customerProvider);
    }

    function it_is_reviewer_subject_provider(): void
    {
        $this->shouldImplement(ProductReviewerProviderInterface::class);
    }

    function it_provides_product_reviewer(Customer $reviewer, CustomerProviderInterface $customerProvider): void
    {
        $customerProvider->provide('example@customer.com')->willReturn($reviewer);

        $this->provide('example@customer.com')->shouldReturn($reviewer);
    }
}
