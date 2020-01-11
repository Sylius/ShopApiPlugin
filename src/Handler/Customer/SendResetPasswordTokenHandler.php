<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Customer;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Customer\SendResetPasswordToken;
use Sylius\ShopApiPlugin\Mailer\Emails;
use Webmozart\Assert\Assert;

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

        /** @var ShopUserInterface $user */
        $user = $this->userRepository->findOneByEmail($email);
        if (null === $user) {
            return;
        }
        Assert::notNull($user->getPasswordResetToken(), sprintf('User with %s email has not verification token defined.', $email));
        $this->sender->send(
            Emails::EMAIL_RESET_PASSWORD_TOKEN,
            [$email],
            ['user' => $user, 'channelCode' => $resendResetPasswordToken->channelCode()]
        );
    }

}
