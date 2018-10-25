<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Command\SendResetPasswordToken;
use Sylius\ShopApiPlugin\Handler\SendResetPasswordTokenHandler;
use Sylius\ShopApiPlugin\Mailer\Emails;

final class SendResetPasswordTokenHandlerSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository, SenderInterface $sender): void
    {
        $this->beConstructedWith($userRepository, $sender);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SendResetPasswordTokenHandler::class);
    }

    function it_handles_emailing_user_with_verification_email(
        UserRepositoryInterface $userRepository,
        SenderInterface $sender,
        ShopUserInterface $user
    ): void {
        $userRepository->findOneByEmail('example@customer.com')->willReturn($user);
        $user->getPasswordResetToken()->willReturn('SOMERANDOMSTRINGASDAFSASFAFAFAACEAFCCEFACVAFVSF');

        $sender->send(Emails::EMAIL_RESET_PASSWORD_TOKEN, ['example@customer.com'], ['user' => $user])->shouldBeCalled();

        $this->handle(new SendResetPasswordToken('example@customer.com'));
    }

    function it_throws_an_exception_if_user_has_not_been_found(
        UserRepositoryInterface $userRepository
    ): void {
        $userRepository->findOneByEmail('example@customer.com')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new SendResetPasswordToken('example@customer.com')]);
    }

    function it_throws_an_exception_if_user_has_not_verification_token(
        UserRepositoryInterface $userRepository,
        ShopUserInterface $user
    ): void {
        $userRepository->findOneByEmail('example@customer.com')->willReturn($user);
        $user->getPasswordResetToken()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new SendResetPasswordToken('example@customer.com')]);
    }
}
