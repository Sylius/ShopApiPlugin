<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\SetDefaultAddress;
use Sylius\ShopApiPlugin\Request\SetDefaultAddressRequest;
use Symfony\Component\HttpFoundation\Request;

final class SetDefaultAddressRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_set_default_address_command()
    {
        $setDefaultAddressRequest = new SetDefaultAddressRequest(new Request([], [], ['id' => '1']));

        $this->assertEquals($setDefaultAddressRequest->getCommand(), new SetDefaultAddress('1'));
    }
}
