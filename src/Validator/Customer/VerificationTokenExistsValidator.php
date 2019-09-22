<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Customer;

use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class VerificationTokenExistsValidator extends ConstraintValidator
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($token, Constraint $constraint)
    {
        if (null === $token || 0 === strlen($token)) {
            return;
        }
        if (null === $this->userRepository->findOneBy(['emailVerificationToken' => $token])) {
            $this->context->addViolation($constraint->message);
        }
    }
}
