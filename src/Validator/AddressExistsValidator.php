<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInUserProviderInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\AddressExists;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AddressExistsValidator extends ConstraintValidator
{
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;
    /**
     * @var LoggedInUserProviderInterface
     */
    private $currentUserProvider;

    /**
     * AddressExistsValidator constructor.
     *
     * @param AddressRepositoryInterface    $addressRepository
     * @param LoggedInUserProviderInterface $currentUserProvider
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        LoggedInUserProviderInterface $currentUserProvider
    ) {
        $this->addressRepository = $addressRepository;
        $this->currentUserProvider = $currentUserProvider;
    }

    /**
     * @param mixed $id
     * @param Constraint|AddressExists $constraint
     */
    public function validate($id, Constraint $constraint)
    {
        $address = $this->addressRepository->findOneBy(['id' => $id]);

        if (!$address instanceof AddressInterface) {
            return $this->context->addViolation($constraint->message);
        }

        $user = $this->currentUserProvider->provide();

        if ($address->getCustomer()->getEmail() !== $user->getEmail()) {
            return $this->context->addViolation($constraint->message);
        }
    }
}
