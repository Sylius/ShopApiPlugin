<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    public function validate(mixed $code, Constraint $constraint): void
    {
        $country = $this->countryRepository->findOneBy(['code' => $code]);

        if ($country === null) {
            $this->context->addViolation($constraint->message);
        }
    }
}
