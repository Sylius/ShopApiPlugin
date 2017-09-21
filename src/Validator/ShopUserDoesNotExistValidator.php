<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Validator;

use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ShopUserDoesNotExistValidator extends ConstraintValidator
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($email, Constraint $constraint)
    {
        if (null !== $this->userRepository->findOneByEmail($email)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
