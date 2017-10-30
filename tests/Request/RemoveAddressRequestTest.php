<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\AddressBook;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\RemoveAddress;
use Sylius\ShopApiPlugin\Request\RemoveAddressRequest;
use Symfony\Component\HttpFoundation\Request;

class RemoveAddressRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_remove_address_command()
    {
        $removeAddressRequest = new RemoveAddressRequest(new Request([], [], ['id' => '1']));

        $this->assertEquals($removeAddressRequest->getCommand(), new RemoveAddress('1'));
    }
}