<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Sylius\ShopApiPlugin\Command\Customer\SendVerificationToken;
use Sylius\ShopApiPlugin\Request\Customer\ResendVerificationTokenRequest;
use Symfony\Component\HttpFoundation\Request;

final class ResendVerificationTokenRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_resend_verification_token_request_command(): void
    {
        $channel = new Channel();
        $channel->setCode('WEB_GB');

        $request = ResendVerificationTokenRequest::fromHttpRequestAndChannel(
            new Request([], ['email' => 'daffy@the-duck.com']),
            $channel
        );

        $this->assertEquals(
            $request->getCommand(),
            new SendVerificationToken('daffy@the-duck.com', 'WEB_GB')
        );
    }
}
