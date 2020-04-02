<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Customer;

use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShopUserExistsValidator extends ConstraintValidator
{

    /** @var UserRepositoryInterface */
    private $userRepository;
    /** @var TranslatorInterface  */
    private $translator;

    public function __construct(
        UserRepositoryInterface $userRepository,
        TranslatorInterface $translator
    ) {
        $this->userRepository = $userRepository;
        $this->translator     = $translator;
    }

    public function validate($string, Constraint $constraint)
    {
        if (null === $string || null === $this->userRepository->findOneByEmail($string)) {
            $this->context->addViolation($this->translator->trans($constraint->message));
        }
    }
}
