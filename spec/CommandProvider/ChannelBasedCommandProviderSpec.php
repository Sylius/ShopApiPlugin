<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\CommandProvider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\CommandProvider\ChannelBasedCommandProviderInterface;
use Sylius\ShopApiPlugin\Request\Cart\PickupCartRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ChannelBasedCommandProviderSpec extends ObjectBehavior
{
    function let(ValidatorInterface $validator): void
    {
        $this->beConstructedWith(PickupCartRequest::class, $validator);
    }

    function it_implements_channel_command_provider_interface(): void
    {
        $this->shouldHaveType(ChannelBasedCommandProviderInterface::class);
    }

    function it_validates_request(
        ValidatorInterface $validator,
        Request $request,
        ConstraintViolationListInterface $constraintViolationList,
        ChannelInterface $channel
    ): void {
        $channel->getCode()->willReturn('WEB_GB');

        $validator
            ->validate(Argument::type(PickupCartRequest::class))
            ->willReturn($constraintViolationList)
        ;

        $this->validate($request, $channel)->shouldReturn($constraintViolationList);
    }
}
