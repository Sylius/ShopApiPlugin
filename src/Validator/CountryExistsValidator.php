<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\AddressExists;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CountryExistsValidator extends ConstraintValidator
{
    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * CountryExistsValidator constructor.
     *
     * @param RepositoryInterface $countryRepository
     */
    public function __construct(RepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * Validates if the country exists
     *
     * @param mixed                    $id
     * @param Constraint|AddressExists $constraint
     */
    public function validate($id, Constraint $constraint)
    {
        $country = $this->countryRepository->findOneBy(['id' => $id]);

        if ($country === null) {
            return $this->context->addViolation($constraint->message);
        }
    }
}
