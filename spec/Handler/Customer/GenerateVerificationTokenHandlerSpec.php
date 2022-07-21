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
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Sylius\ShopApiPlugin\Command\Customer\GenerateVerificationToken;
use Sylius\ShopApiPlugin\Handler\Customer\GenerateVerificationTokenHandler;

final class GenerateVerificationTokenHandlerSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository, GeneratorInterface $tokenGenerator): void
    {
        $this->beConstructedWith($userRepository, $tokenGenerator);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(GenerateVerificationTokenHandler::class);
    }

    function it_handles_generating_user_verification_token(
        UserRepositoryInterface $userRepository,
        GeneratorInterface $tokenGenerator,
        ShopUserInterface $user,
    ): void {
        $userRepository->findOneByEmail('example@customer.com')->willReturn($user);

        $tokenGenerator->generate()->willReturn('SOMERANDOMSTRINGASDAFSASFAFAFAACEAFCCEFACVAFVSF');

        $user->setEmailVerificationToken('SOMERANDOMSTRINGASDAFSASFAFAFAACEAFCCEFACVAFVSF')->shouldBeCalled();

        $this(new GenerateVerificationToken('example@customer.com'));
    }

    function it_throws_an_exception_if_user_has_not_been_found(
        UserRepositoryInterface $userRepository,
    ): void {
        $userRepository->findOneByEmail('example@customer.com')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [new GenerateVerificationToken('example@customer.com')]);
    }
}
