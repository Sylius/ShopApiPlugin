<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\AddressBook;

use PhpSpec\ObjectBehavior;

final class SetDefaultAddressSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('ADDRESS_ID', 'user@email.com');
    }

    function it_has_id(): void
    {
        $this->id()->shouldReturn('ADDRESS_ID');
    }

    function it_has_user_email(): void
    {
        $this->userEmail()->shouldReturn('user@email.com');
    }
}
