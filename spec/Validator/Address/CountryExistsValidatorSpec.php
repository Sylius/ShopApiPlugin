<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Address;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CountryExists;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CountryExistsValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, RepositoryInterface $countryRepository): void
    {
        $this->beConstructedWith($countryRepository);

        $this->initialize($executionContext);
    }

    function it_does_not_add_constraint_if_order_exists(
        CountryInterface $country,
        RepositoryInterface $countryRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $countryRepository->findOneBy(['code' => 'DE'])->willReturn($country);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('DE', new CountryExists());
    }

    function it_adds_constraint_if_order_does_not_exits_exists(
        RepositoryInterface $countryRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $countryRepository->findOneBy(['code' => 'XY'])->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.country.not_exists')->shouldBeCalled();

        $this->validate('XY', new CountryExists());
    }
}
