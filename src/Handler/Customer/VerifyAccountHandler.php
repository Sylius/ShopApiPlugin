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
use Sylius\ShopApiPlugin\Command\Customer\VerifyAccount;
use Webmozart\Assert\Assert;

final class VerifyAccountHandler
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(VerifyAccount $resendVerificationToken): void
    {
        /** @var ShopUserInterface $user */
        $user = $this->userRepository->findOneBy(['emailVerificationToken' => $resendVerificationToken->token()]);

        Assert::notNull($user, sprintf('User has not been found.'));

        $user->setVerifiedAt(new \DateTime());
        $user->setEmailVerificationToken(null);
        $user->enable();
    }
}
