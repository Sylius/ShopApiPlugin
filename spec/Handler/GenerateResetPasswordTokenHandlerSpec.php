<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Sylius\ShopApiPlugin\Command\GenerateResetPasswordToken;
use Sylius\ShopApiPlugin\Handler\GenerateResetPasswordTokenHandler;

final class GenerateResetPasswordTokenHandlerSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository, GeneratorInterface $tokenGenerator)
    {
        $this->beConstructedWith($userRepository, $tokenGenerator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GenerateResetPasswordTokenHandler::class);
    }

    function it_handles_generating_user_verification_token(
        UserRepositoryInterface $userRepository,
        GeneratorInterface $tokenGenerator,
        ShopUserInterface $user
    ) {
        $userRepository->findOneByEmail('example@customer.com')->willReturn($user);

        $tokenGenerator->generate()->willReturn('RANDOM_TOKEN');

        $user->setPasswordResetToken('RANDOM_TOKEN')->shouldBeCalled();
        $user->setPasswordRequestedAt(Argument::type(\DateTime::class))->shouldBeCalled();

        $this->handle(new GenerateResetPasswordToken('example@customer.com'));
    }

    function it_throws_an_exception_if_user_has_not_been_found(
        UserRepositoryInterface $userRepository
    ) {
        $userRepository->findOneByEmail('example@customer.com')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new GenerateResetPasswordToken('example@customer.com')]);
    }
}
