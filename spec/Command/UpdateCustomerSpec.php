<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;

final class UpdateCustomerSpec extends ObjectBehavior
{
    function let()
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

    function it_has_first_name()
    {
        $this->firstName()->shouldReturn('Sherlock');
    }

    function it_has_last_name()
    {
        $this->lastName()->shouldReturn('Holmes');
    }

    function it_has_email()
    {
        $this->email()->shouldReturn('sherlock@holmes.com');
    }

    function it_has_gender()
    {
        $this->gender()->shouldReturn('m');
    }

    function it_has_birthday()
    {
        $this->birthday()->shouldReturn('2017-11-01');
    }

    function it_has_phone_number()
    {
        $this->phoneNumber()->shouldReturn('091231512512');
    }

    function it_has_subscribed_to_newsletter()
    {
        $this->subscribedToNewsletter()->shouldReturn(true);
    }
}
