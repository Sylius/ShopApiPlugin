<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Customer;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Sylius\ShopApiPlugin\Command\Customer\GenerateResetPasswordToken;
use Sylius\ShopApiPlugin\Exception\UserNotFoundException;

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

        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneByEmail($email);
        if (null === $user) {
            throw UserNotFoundException::withEmail($email);
        }
        $user->setPasswordResetToken($this->tokenGenerator->generate());
        $user->setPasswordRequestedAt(new \DateTime());
    }
}
