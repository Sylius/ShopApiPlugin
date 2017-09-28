<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Sylius\ShopApiPlugin\Command\GenerateResetPasswordToken;
use Webmozart\Assert\Assert;

final class GenerateResetPasswordTokenHandler
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var GeneratorInterface
     */
    private $tokenGenerator;

    public function __construct(UserRepositoryInterface $userRepository, GeneratorInterface $tokenGenerator)
    {
        $this->userRepository = $userRepository;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function handle(GenerateResetPasswordToken $generateResetPasswordToken)
    {
        $email = $generateResetPasswordToken->email();

        /** @var ShopUserInterface $user */
        $user = $this->userRepository->findOneByEmail($email);

        Assert::notNull($user, sprintf('User with %s email has not been found.', $email));

        $user->setPasswordResetToken($this->tokenGenerator->generate());
        $user->setPasswordRequestedAt(new \DateTime());
    }
}
