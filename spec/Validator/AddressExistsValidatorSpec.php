<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInUserProviderInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\AddressExists;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AddressExistsValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
        AddressRepositoryInterface $addressRepository,
        LoggedInUserProviderInterface $currentUserProvider
    ): void {
        $this->beConstructedWith($addressRepository, $currentUserProvider);

        $this->initialize($executionContext);
    }

    function it_does_not_add_constraint_if_address_exists_and_its_owned_by_current_user(
        AddressInterface $address,
        ShopUserInterface $shopUser,
        CustomerInterface $customerOwner,
        LoggedInUserProviderInterface $currentUserProvider,
        AddressRepositoryInterface $addressRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $customerOwner->getEmail()->willReturn('oliver@queen.com');
        $address->getCustomer()->willReturn($customerOwner);

        $shopUser->getEmail()->willReturn('oliver@queen.com');
        $currentUserProvider->provide()->willReturn($shopUser);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('ADDRESS_ID', new AddressExists());
    }

    function it_adds_constraint_if_address_does_not_exits_exists(
        AddressRepositoryInterface $addressRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.address.not_exists')->shouldBeCalled();

        $this->validate('ADDRESS_ID', new AddressExists());
    }

    function it_adds_constraint_if_current_user_is_not_address_owner(
        AddressInterface $address,
        AddressRepositoryInterface $addressRepository,
        LoggedInUserProviderInterface $currentUserProvider,
        ShopUserInterface $shopUser,
        CustomerInterface $customerOwner,
        ExecutionContextInterface $executionContext
    ): void {
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $customerOwner->getEmail()->willReturn('oliver@queen.com');
        $address->getCustomer()->willReturn($customerOwner);

        $currentUserProvider->provide()->willReturn($shopUser);

        $shopUser->getEmail()->willReturn('shop@example.com');

        $executionContext->addViolation('sylius.shop_api.address.not_exists')->shouldBeCalled();

        $this->validate('ADDRESS_ID', new AddressExists());
    }
}
