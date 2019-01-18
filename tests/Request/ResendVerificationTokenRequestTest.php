<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\SendVerificationToken;
use Sylius\ShopApiPlugin\Request\ResendVerificationTokenRequest;
use Symfony\Component\HttpFoundation\Request;

final class ResendVerificationTokenRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_put_simple_item_to_cart_command()
    {
        $putSimpleItemToCartRequest = new ResendVerificationTokenRequest();
        $putSimpleItemToCartRequest->populateData(new Request([], ['email' => 'daffy@the-duck.com'], ['channelCode' => 'WEB_GB']));

        $this->assertEquals($putSimpleItemToCartRequest->getCommand(), new SendVerificationToken('daffy@the-duck.com', 'WEB_GB'));
    }
}
