<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Command\SendVerificationToken;
use Sylius\ShopApiPlugin\Mailer\Emails;
use Webmozart\Assert\Assert;

final class SendVerificationTokenHandler
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var SenderInterface
     */
    private $sender;

    public function __construct(UserRepositoryInterface $userRepository, SenderInterface $sender)
    {
        $this->userRepository = $userRepository;
        $this->sender = $sender;
    }

    public function handle(SendVerificationToken $resendVerificationToken)
    {
        $email = $resendVerificationToken->email();

        /** @var ShopUserInterface $user */
        $user = $this->userRepository->findOneByEmail($email);

        Assert::notNull($user, sprintf('User with %s email has not been found.', $email));
        Assert::notNull($user->getEmailVerificationToken(), sprintf('User with %s email has not verification token defined.', $email));

        $this->sender->send(Emails::EMAIL_VERIFICATION_TOKEN, [$email], ['user' => $user]);
    }
}
