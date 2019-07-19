<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
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
    function let(ValidatorInterface $validator, MessageBusInterface $bus, ChannelContextInterface $channelContext): void
    {
        $this->beConstructedWith($validator, $bus, $channelContext);
    }

    function it_implements_request_cart_token_normalizer_interface(): void
    {
        $this->shouldImplement(RequestCartTokenNormalizerInterface::class);
    }

    function it_returns_passed_request_if_cart_token_was_set(
        ValidatorInterface $validator,
        MessageBusInterface $bus,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        Request $request
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_GB');

        $request->attributes = new ParameterBag(['token' => 'sample_cart_token']);

        $validator->validate(Argument::any())->shouldNotBeCalled();
        $bus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->doNotAllowNullCartToken($request)->shouldReturn($request);
    }

    function it_picks_up_new_cart_and_sets_its_token_on_request_if_token_was_not_passed(
        ValidatorInterface $validator,
        MessageBusInterface $bus,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        Request $request,
        ConstraintViolationListInterface $constraintViolationList
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_GB');

        $request->attributes = new ParameterBag();

        $constraintViolationList->count()->willReturn(0);

        $validator->validate(Argument::type(PickupCartRequest::class))->willReturn($constraintViolationList);

        $bus
            ->dispatch(Argument::that(function (PickupCart $command): bool {
                return !empty($command->orderToken()) && $command->channelCode() === 'WEB_GB';
            }))
            ->willReturn(new Envelope(new \stdClass()))
            ->shouldBeCalled()
        ;

        $this->doNotAllowNullCartToken($request);
    }
}
