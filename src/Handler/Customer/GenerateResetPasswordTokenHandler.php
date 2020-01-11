<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Customer;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Sylius\ShopApiPlugin\Command\Customer\GenerateResetPasswordToken;

final class GenerateResetPasswordTokenHandler
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var GeneratorInterface */
    private $tokenGenerator;

    public function __construct(UserRepositoryInterface $userRepository, GeneratorInterface $tokenGenerator)
    {
        $this->userRepository = $userRepository;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function __invoke(GenerateResetPasswordToken $generateResetPasswordToken): void
    {
        $email = $generateResetPasswordToken->email();

        /** @var ShopUserInterface $user */
        $user = $this->userRepository->findOneByEmail($email);
        if (null === $user) {
            return;
        }
        $user->setPasswordResetToken($this->tokenGenerator->generate());
        $user->setPasswordRequestedAt(new \DateTime());

    }
}
