<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator;

use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AddressExistsValidator extends ConstraintValidator
{
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * AddressExistsValidator constructor.
     *
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(AddressRepositoryInterface $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($id, Constraint $constraint)
    {
        if (null === $this->addressRepository->findOneBy(['id' => $id])) {
            $this->context->addViolation($constraint->message);
        }
    }
}
