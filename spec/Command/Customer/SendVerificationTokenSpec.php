<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Customer;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\Customer\SendVerificationToken;

final class SendVerificationTokenSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('example@customer.com', 'WEB_GB');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SendVerificationToken::class);
    }

    function it_has_email(): void
    {
        $this->email()->shouldReturn('example@customer.com');
    }

    function it_has_channel_code(): void
    {
        $this->channelCode()->shouldReturn('WEB_GB');
    }
}
