<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Customer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Customer\EnableCustomer;

final class EnableCustomerHandlerSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository): void
    {
        $this->beConstructedWith($userRepository);
    }

    function it_handles_enabling_user(UserRepositoryInterface $userRepository, ShopUserInterface $user): void
    {
        $userRepository->findOneByEmail('shop@example.com')->willReturn($user);

        $user->enable()->shouldBeCalled();

        $this(new EnableCustomer('shop@example.com'));
    }

    function it_throws_an_exception_if_user_cannot_be_found(UserRepositoryInterface $userRepository): void
    {
        $userRepository->findOneByEmail('shop@example.com')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new EnableCustomer('shop@example.com'),
        ]);
    }
}
