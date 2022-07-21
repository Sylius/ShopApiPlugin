<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Customer;

use InvalidArgumentException;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Customer\SendResetPasswordToken;
use Sylius\ShopApiPlugin\Exception\UserNotFoundException;
use Sylius\ShopApiPlugin\Mailer\Emails;

final class SendResetPasswordTokenHandler
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var SenderInterface */
    private $sender;

    public function __construct(UserRepositoryInterface $userRepository, SenderInterface $sender)
    {
        $this->userRepository = $userRepository;
        $this->sender = $sender;
    }

    public function __invoke(SendResetPasswordToken $resendResetPasswordToken): void
    {
        $email = $resendResetPasswordToken->email();

        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneByEmail($email);
        if (null === $user) {
            throw UserNotFoundException::withEmail($email);
        }
        if (null === $user->getPasswordResetToken()) {
            throw new InvalidArgumentException(sprintf('User with %s email has not verification token defined.', $email));
        }
        $this->sender->send(
            Emails::EMAIL_RESET_PASSWORD_TOKEN,
            [$email],
            ['user' => $user, 'channelCode' => $resendResetPasswordToken->channelCode()],
        );
    }
}
