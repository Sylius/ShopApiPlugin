<?php

declare(strict_types=1);

namespace Tests\Sylius\SyliusShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\SyliusShopApiPlugin\Command\SendVerificationToken;
use Sylius\SyliusShopApiPlugin\Request\ResendVerificationTokenRequest;
use Symfony\Component\HttpFoundation\Request;

final class ResendVerificationTokenRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_put_simple_item_to_cart_command()
    {
        $putSimpleItemToCartRequest = new ResendVerificationTokenRequest(new Request([], ['email' => 'daffy@the-duck.com'], []));

        $this->assertEquals($putSimpleItemToCartRequest->getCommand(), new SendVerificationToken('daffy@the-duck.com'));
    }
}
