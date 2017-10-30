<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\RemoveAddress;
use Sylius\ShopApiPlugin\Request\RemoveAddressRequest;
use Symfony\Component\HttpFoundation\Request;

class ShowAddressBookRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_show_address_book_command()
    {
        $removeAddressRequest = new RemoveAddressRequest(new Request([], ['id' => '1']));

        $this->assertEquals($removeAddressRequest->getCommand(), new RemoveAddress('1'));
    }
}
