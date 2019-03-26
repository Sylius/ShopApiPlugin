<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
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
        $updateCustomerRequest = new UpdateCustomerRequest(new Request([], [
            'firstName' => 'ivan',
            'lastName' => 'Mts',
            'email' => 'ivan.matas@locastic.com',
            'birthday' => '2017-11-01',
            'gender' => 'm',
            'phoneNumber' => '125125112',
            'subscribedToNewsletter' => true,
        ], []))
        ;
        $this->assertEquals($updateCustomerRequest->getCommand(), new UpdateCustomer(
            'ivan',
            'Mts',
            'ivan.matas@locastic.com',
            '2017-11-01',
            'm',
            '125125112',
            true))
        ;
    }
}
