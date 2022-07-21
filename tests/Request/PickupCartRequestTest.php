<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Sylius\ShopApiPlugin\Request\Cart\PickupCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class PickupCartRequestTest extends TestCase
{
    /** @test */
    public function it_creates_pickup_cart_command(): void
    {
        $channel = new Channel();
        $channel->setCode('WEB_GB');

        $pickupCartRequest = PickupCartRequest::fromHttpRequestAndChannel(new Request(), $channel);

        $pickupCartCommand = $pickupCartRequest->getCommand();

        $this->assertEquals('WEB_GB', $pickupCartCommand->channelCode());
        $this->assertNotNull($pickupCartCommand->orderToken());
    }
}
