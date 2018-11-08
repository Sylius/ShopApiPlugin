<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Address;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CountryExistsValidator extends ConstraintValidator
{
    /** @var RepositoryInterface */
    private $countryRepository;

    public function __construct(RepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function validate($code, Constraint $constraint)
    {
        $country = $this->countryRepository->findOneBy(['code' => $code]);

        if ($country === null) {
            return $this->context->addViolation($constraint->message);
        }
    }
}
