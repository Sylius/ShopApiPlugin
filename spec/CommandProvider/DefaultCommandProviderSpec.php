<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\CommandProvider;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Sylius\ShopApiPlugin\Mocks\TestRequest;

final class DefaultCommandProviderSpec extends ObjectBehavior
{
    function let(ValidatorInterface $validator): void
    {
        $this->beConstructedWith(TestRequest::class, $validator);
    }

    function it_implements_command_provider_interface(): void
    {
        $this->shouldHaveType(CommandProviderInterface::class);
    }

    function it_validates_request(
        ValidatorInterface $validator,
        Request $httpRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): void {
        $httpRequest->attributes = new ParameterBag(['token' => 'sample_cart_token']);

        $validator
            ->validate(TestRequest::fromHttpRequest($httpRequest->getWrappedObject()))
            ->willReturn($constraintViolationList)
        ;

        $this->validate($httpRequest)->shouldReturn($constraintViolationList);
    }
}
