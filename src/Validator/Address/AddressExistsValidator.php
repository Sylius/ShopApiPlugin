<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Address;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\AddressExists;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AddressExistsValidator extends ConstraintValidator
{
    /** @var AddressRepositoryInterface */
    private $addressRepository;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInUserProvider;

    public function __construct(
        AddressRepositoryInterface $addressRepository,
        LoggedInShopUserProviderInterface $loggedInUserProvider
    ) {
        $this->addressRepository = $addressRepository;
        $this->loggedInUserProvider = $loggedInUserProvider;
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

        $user = $this->loggedInUserProvider->provide();

        if ($address->getCustomer()->getEmail() !== $user->getEmail()) {
            return $this->context->addViolation($constraint->message);
        }
    }
}
