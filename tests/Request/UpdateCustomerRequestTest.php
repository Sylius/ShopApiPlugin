<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\ShopApiPlugin\Command\Customer\UpdateCustomer;
use Sylius\ShopApiPlugin\Request\Customer\UpdateCustomerRequest;
use Symfony\Component\HttpFoundation\Request;

final class UpdateCustomerRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_update_customer_command()
    {
        $shopUser = new ShopUser();
        $customer = new Customer();
        $customer->setEmail('ivan.matas@locastic.com');
        $shopUser->setCustomer($customer);

        $updateCustomerRequest = UpdateCustomerRequest::fromHttpRequestAndShopUser(new Request([], [
            'firstName' => 'ivan',
            'lastName' => 'Mts',
            'birthday' => '2017-11-01',
            'gender' => 'm',
            'phoneNumber' => '125125112',
            'subscribedToNewsletter' => true,
        ], []), $shopUser);

        $this->assertEquals($updateCustomerRequest->getCommand(), new UpdateCustomer(
            'ivan',
            'Mts',
            'ivan.matas@locastic.com',
            new DateTimeImmutable('2017-11-01'),
            'm',
            '125125112',
            true))
        ;
    }
}
