<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\ShopUserInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Sylius\ShopApiPlugin\Command\GenerateVerificationToken;
use Sylius\ShopApiPlugin\Handler\GenerateVerificationTokenHandler;

final class GenerateVerificationTokenHandlerSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository, GeneratorInterface $tokenGenerator) {
        $this->beConstructedWith($userRepository, $tokenGenerator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GenerateVerificationTokenHandler::class);
    }

    function it_handles_generating_user_verification_token(
        UserRepositoryInterface $userRepository,
        GeneratorInterface $tokenGenerator,
        ShopUserInterface $user
    ): void {
        $userRepository->findOneByEmail('example@customer.com')->willReturn($user);

        $tokenGenerator->generate()->willReturn('SOMERANDOMSTRINGASDAFSASFAFAFAACEAFCCEFACVAFVSF');

        $user->setEmailVerificationToken('SOMERANDOMSTRINGASDAFSASFAFAFAACEAFCCEFACVAFVSF')->shouldBeCalled();

        $this->handle(new GenerateVerificationToken('example@customer.com'));
    }

    function it_throws_an_exception_if_user_has_not_been_found(
        UserRepositoryInterface $userRepository
    ): void {
        $userRepository->findOneByEmail('example@customer.com')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new GenerateVerificationToken('example@customer.com')]);
    }
}
