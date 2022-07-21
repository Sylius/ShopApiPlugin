<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Customer;

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Customer\SendResetPasswordToken;
use Sylius\ShopApiPlugin\Exception\UserNotFoundException;
use Sylius\ShopApiPlugin\Handler\Customer\SendResetPasswordTokenHandler;
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
        ShopUserInterface $user,
    ): void {
        $userRepository->findOneByEmail('example@customer.com')->willReturn($user);
        $user->getPasswordResetToken()->willReturn('SOMERANDOMSTRINGASDAFSASFAFAFAACEAFCCEFACVAFVSF');

        $sender->send(Emails::EMAIL_RESET_PASSWORD_TOKEN, ['example@customer.com'], ['user' => $user, 'channelCode' => 'WEB_GB'])->shouldBeCalled();

        $this(new SendResetPasswordToken('example@customer.com', 'WEB_GB'));
    }

    function it_throws_an_exception_if_user_has_not_verification_token(
        UserRepositoryInterface $userRepository,
        ShopUserInterface $user,
    ): void {
        $userRepository->findOneByEmail('example@customer.com')->willReturn($user);
        $user->getPasswordResetToken()->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)->during('__invoke', [new SendResetPasswordToken('example@customer.com', 'WEB_GB')]);
    }

    function it_throws_an_exception_if_user_has_not_been_found(
        UserRepositoryInterface $userRepository,
    ): void {
        $userRepository->findOneByEmail('example@customer.com')->willReturn(null);
        $this->shouldThrow(UserNotFoundException::class)->during('__invoke', [new SendResetPasswordToken('example@customer.com', 'WEB_GB')]);
    }
}
