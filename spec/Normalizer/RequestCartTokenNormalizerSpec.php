<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Normalizer;

use League\Tactician\CommandBus;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Normalizer\RequestCartTokenNormalizerInterface;
use Sylius\ShopApiPlugin\Request\PickupCartRequest;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestCartTokenNormalizerSpec extends ObjectBehavior
{
    public function let(ValidatorInterface $validator, CommandBus $bus): void
    {
        $this->beConstructedWith($validator, $bus);
    }

    public function it_implements_request_cart_token_normalizer_interface(): void
    {
        $this->shouldImplement(RequestCartTokenNormalizerInterface::class);
    }

    public function it_returns_passed_request_if_cart_token_was_set(
        Request $request,
        ValidatorInterface $validator,
        CommandBus $bus
    ): void {
        $request->attributes = new ParameterBag([
            'token' => 'sample_cart_token',
            'channel' => 'en_GB',
        ]);

        $validator->validate(Argument::any())->shouldNotBeCalled();
        $bus->handle(Argument::any())->shouldNotBeCalled();

        $this->doNotAllowNullCartToken($request)->shouldReturn($request);
    }

    public function it_throws_exception_when_pickup_cart_request_is_not_valid(
        Request $request,
        ValidatorInterface $validator,
        CommandBus $bus,
        ConstraintViolationListInterface $constraintViolationList
    ): void {
        $request->attributes = new ParameterBag([]);
        $request->request = new ParameterBag(['channel' => 'non_existing_channel']);

        $constraintViolationList->count()->willReturn(1);

        $validator->validate(Argument::type(PickupCartRequest::class))->willReturn($constraintViolationList);

        $bus->handle(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('doNotAllowNullCartToken', [$request]);
    }

    public function it_picks_up_new_cart_and_sets_its_token_on_request_if_token_was_not_passed(
        Request $request,
        ValidatorInterface $validator,
        CommandBus $bus,
        ConstraintViolationListInterface $constraintViolationList
    ): void {
        $request->attributes = new ParameterBag([]);
        $request->request = new ParameterBag(['channel' => 'en_GB']);

        $constraintViolationList->count()->willReturn(0);

        $validator->validate(Argument::type(PickupCartRequest::class))->willReturn($constraintViolationList);

        $bus->handle(Argument::that(function (PickupCart $pickupCart): bool {
            return
                !empty($pickupCart->orderToken()) &&
                $pickupCart->channelCode() === 'en_GB'
            ;
        }))->shouldBeCalled();

        $this->doNotAllowNullCartToken($request);
    }
}
