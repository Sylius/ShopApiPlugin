<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\CommandProvider\ChannelBasedCommandProviderInterface;
use Sylius\ShopApiPlugin\Normalizer\RequestCartTokenNormalizerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class RequestCartTokenNormalizerSpec extends ObjectBehavior
{
    function let(
        MessageBusInterface $bus,
        ChannelContextInterface $channelContext,
        ChannelBasedCommandProviderInterface $pickupCartCommandProvider,
    ): void {
        $this->beConstructedWith($bus, $channelContext, $pickupCartCommandProvider);
    }

    function it_implements_request_cart_token_normalizer_interface(): void
    {
        $this->shouldImplement(RequestCartTokenNormalizerInterface::class);
    }

    function it_returns_passed_request_if_cart_token_was_set(
        MessageBusInterface $bus,
        ChannelContextInterface $channelContext,
        ChannelBasedCommandProviderInterface $pickupCartCommandProvider,
        ChannelInterface $channel,
        Request $request,
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_GB');

        $request->attributes = new ParameterBag(['token' => 'sample_cart_token']);

        $pickupCartCommandProvider->validate($request, $channel)->shouldNotBeCalled();
        $bus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->doNotAllowNullCartToken($request)->shouldReturn($request);
    }

    function it_picks_up_new_cart_and_sets_its_token_on_request_if_token_was_not_passed(
        MessageBusInterface $bus,
        ChannelContextInterface $channelContext,
        ChannelBasedCommandProviderInterface $pickupCartCommandProvider,
        ChannelInterface $channel,
        Request $request,
        ConstraintViolationListInterface $constraintViolationList,
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_GB');

        $request->attributes = new ParameterBag();

        $constraintViolationList->count()->willReturn(0);

        $command = new PickupCart('4fe1bb6e-2f27-4bd4-b1ff-7de842498573', 'WEB_GB');

        $pickupCartCommandProvider->validate($request, $channel)->willReturn($constraintViolationList);
        $pickupCartCommandProvider->getCommand($request, $channel)->willReturn($command);

        $bus
            ->dispatch($command)
            ->willReturn(new Envelope(new \stdClass()))
            ->shouldBeCalled()
        ;

        $this->doNotAllowNullCartToken($request);
    }
}
