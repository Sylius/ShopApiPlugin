<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Customer;

use PhpSpec\ObjectBehavior;

final class UpdateCustomerSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
                'Sherlock',
                'Holmes',
                'sherlock@holmes.com',
                '2017-11-01',
                'm',
                '091231512512',
                true
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

    function it_has_birthday(): void
    {
        $this->birthday()->shouldReturn('2017-11-01');
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
