<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\Customer\VerifyAccount;
use Sylius\ShopApiPlugin\Request\Customer\VerifyAccountRequest;
use Symfony\Component\HttpFoundation\Request;

final class VerifyAccountRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_verify_account_command()
    {
        $request = VerifyAccountRequest::fromHttpRequest(new Request(['token' => 'RANDOMSTRINGAFAFAKASNFJAFAJ'], [], []));

        $this->assertEquals($request->getCommand(), new VerifyAccount('RANDOMSTRINGAFAFAKASNFJAFAJ'));
    }
}
