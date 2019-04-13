<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Normalizer\RequestCartTokenNormalizerInterface;
use Sylius\ShopApiPlugin\Request\Cart\PickupCartRequest;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestCartTokenNormalizerSpec extends ObjectBehavior
{
    function let(ValidatorInterface $validator, MessageBusInterface $bus): void
    {
        $this->beConstructedWith($validator, $bus);
    }

    function it_implements_request_cart_token_normalizer_interface(): void
    {
        $this->shouldImplement(RequestCartTokenNormalizerInterface::class);
    }

    function it_returns_passed_request_if_cart_token_was_set(
        Request $request,
        ValidatorInterface $validator,
        MessageBusInterface $bus
    ): void {
        $request->attributes = new ParameterBag([
            'token' => 'sample_cart_token',
            'channelCode' => 'en_GB',
        ]);

        $validator->validate(Argument::any())->shouldNotBeCalled();
        $bus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->doNotAllowNullCartToken($request)->shouldReturn($request);
    }

    function it_throws_exception_when_pickup_cart_request_is_not_valid(
        Request $request,
        ValidatorInterface $validator,
        MessageBusInterface $bus,
        ConstraintViolationListInterface $constraintViolationList
    ): void {
        $request->attributes = new ParameterBag(['channelCode' => 'non_existing_channel']);

        $constraintViolationList->count()->willReturn(1);

        $validator->validate(Argument::type(PickupCartRequest::class))->willReturn($constraintViolationList);

        $bus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('doNotAllowNullCartToken', [$request]);
    }

    function it_picks_up_new_cart_and_sets_its_token_on_request_if_token_was_not_passed(
        Request $request,
        ValidatorInterface $validator,
        MessageBusInterface $bus,
        ConstraintViolationListInterface $constraintViolationList
    ): void {
        $request->attributes = new ParameterBag(['channelCode' => 'en_GB']);

        $constraintViolationList->count()->willReturn(0);

        $validator->validate(Argument::type(PickupCartRequest::class))->willReturn($constraintViolationList);

        $bus
            ->dispatch(Argument::that(function (PickupCart $command): bool {
                return !empty($command->orderToken()) && $command->channelCode() === 'en_GB';
            }))
            ->willReturn(new Envelope(new \stdClass()))
            ->shouldBeCalled()
        ;

        $this->doNotAllowNullCartToken($request);
    }
}
