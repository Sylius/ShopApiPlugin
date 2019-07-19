<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\Customer\SendVerificationToken;
use Sylius\ShopApiPlugin\Request\Customer\ResendVerificationTokenRequest;
use Symfony\Component\HttpFoundation\Request;

final class ResendVerificationTokenRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_put_simple_item_to_cart_command(): void
    {
        $putSimpleItemToCartRequest = new ResendVerificationTokenRequest(
            new Request([], ['email' => 'daffy@the-duck.com']),
            'WEB_GB'
        );

        $this->assertEquals(
            $putSimpleItemToCartRequest->getCommand(),
            new SendVerificationToken('daffy@the-duck.com', 'WEB_GB')
        );
    }
}
