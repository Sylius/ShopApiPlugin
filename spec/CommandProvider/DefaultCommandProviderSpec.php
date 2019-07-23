<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\CommandProvider;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Request\Cart\DropCartRequest;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DefaultCommandProviderSpec extends ObjectBehavior
{
    function let(ValidatorInterface $validator): void
    {
        $this->beConstructedWith(DropCartRequest::class, $validator);
    }

    function it_implements_command_provider_interface(): void
    {
        $this->shouldHaveType(CommandProviderInterface::class);
    }

    function it_validates_request(
        ValidatorInterface $validator,
        Request $request,
        ConstraintViolationListInterface $constraintViolationList
    ): void {
        $request->attributes = new ParameterBag(['token' => 'sample_cart_token']);

        $validator->validate(DropCartRequest::fromRequest($request->getWrappedObject()))->willReturn($constraintViolationList);

        $this->validate($request)->shouldReturn($constraintViolationList);
    }
}
