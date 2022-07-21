<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Customer;

use DateTimeImmutable;
use PhpSpec\ObjectBehavior;

final class UpdateCustomerSpec extends ObjectBehavior
{
    function let(DateTimeImmutable $birthday): void
    {
        $this->beConstructedWith(
            'Sherlock',
            'Holmes',
            'sherlock@holmes.com',
            $birthday,
            'm',
            '091231512512',
            true,
        );
    }

    function it_has_first_name(): void
    {
        $this->firstName()->shouldReturn('Sherlock');
    }

    function it_has_last_name(): void
    {
        $this->lastName()->shouldReturn('Holmes');
    }

    function it_has_email(): void
    {
        $this->email()->shouldReturn('sherlock@holmes.com');
    }

    function it_has_gender(): void
    {
        $this->gender()->shouldReturn('m');
    }

    function it_has_birthday(DateTimeImmutable $birthday): void
    {
        $this->birthday()->shouldReturn($birthday);
    }

    function it_has_phone_number(): void
    {
        $this->phoneNumber()->shouldReturn('091231512512');
    }

    function it_has_subscribed_to_newsletter(): void
    {
        $this->subscribedToNewsletter()->shouldReturn(true);
    }
}
