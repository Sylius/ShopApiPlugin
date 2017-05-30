<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\VerifyAccount;
use Sylius\ShopApiPlugin\Request\VerifyAccountRequest;
use Symfony\Component\HttpFoundation\Request;

final class VerifyAccountRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_put_simple_item_to_cart_command(): void
    {
        $verifyAccountRequest = new VerifyAccountRequest(new Request([], ['token' => 'RANDOMSTRINGAFAFAKASNFJAFAJ'], []));

        $this->assertEquals($verifyAccountRequest->getCommand(), new VerifyAccount('RANDOMSTRINGAFAFAKASNFJAFAJ'));
    }
}
