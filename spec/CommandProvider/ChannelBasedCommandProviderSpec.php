<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\CommandProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\CommandProvider\ChannelBasedCommandProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Sylius\ShopApiPlugin\Mocks\TestChannelBasedRequest;

final class ChannelBasedCommandProviderSpec extends ObjectBehavior
{
    function let(ValidatorInterface $validator): void
    {
        $this->beConstructedWith(TestChannelBasedRequest::class, $validator);
    }

    function it_implements_channel_command_provider_interface(): void
    {
        $this->shouldHaveType(ChannelBasedCommandProviderInterface::class);
    }

    function it_validates_request(
        ValidatorInterface $validator,
        Request $httpRequest,
        ConstraintViolationListInterface $constraintViolationList,
        ChannelInterface $channel,
    ): void {
        $httpRequest->attributes = new ParameterBag(['token' => 'sample_cart_token']);
        $channel->getCode()->willReturn('WEB_GB');

        $validator
            ->validate(
                TestChannelBasedRequest::fromHttpRequestAndChannel(
                    $httpRequest->getWrappedObject(),
                    $channel->getWrappedObject(),
                ),
                null,
                null,
            )
            ->willReturn($constraintViolationList)
        ;

        $this->validate($httpRequest, $channel)->shouldReturn($constraintViolationList);
    }
}
