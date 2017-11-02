<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\AddressExists;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AddressExistsValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, AddressRepositoryInterface $addressRepository)
    {
        $this->beConstructedWith($addressRepository);

        $this->initialize($executionContext);
    }

    function it_does_not_add_constraint_if_address_exists(
        AddressInterface $address,
        AddressRepositoryInterface $addressRepository,
        ExecutionContextInterface $executionContext
    ) {
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('ADDRESS_ID', new AddressExists());
    }

    function it_adds_constraint_if_address_does_not_exits_exists(
        AddressRepositoryInterface $addressRepository,
        ExecutionContextInterface $executionContext
    ) {
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.address.not_exists')->shouldBeCalled();

        $this->validate('ADDRESS_ID', new AddressExists());
    }
}
