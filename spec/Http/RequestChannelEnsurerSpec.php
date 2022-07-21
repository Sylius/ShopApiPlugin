<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Http;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\Checker\ChannelExistenceCheckerInterface;
use Sylius\ShopApiPlugin\Exception\ChannelNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class RequestChannelEnsurerSpec extends ObjectBehavior
{
    function let(ChannelExistenceCheckerInterface $channelExistenceChecker): void
    {
        $this->beConstructedWith($channelExistenceChecker);
    }

    function it_implements_event_subscriber_interface(): void
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_ensures_that_channel_code_passed_in_request_is_valid(
        ChannelExistenceCheckerInterface $channelExistenceChecker,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $event = new ControllerEvent(
            $kernel->getWrappedObject(),
            function () {},
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
        );

        $request->attributes = new ParameterBag(['channelCode' => 'WEB_US']);

        $channelExistenceChecker->withCode('WEB_US')->willThrow(ChannelNotFoundException::class);

        $this
            ->shouldThrow(NotFoundHttpException::class)
            ->during('checkChannelCode', [$event])
        ;
    }

    function it_does_nothing_if_there_is_no_channel_code_in_request_attributes(
        ChannelExistenceCheckerInterface $channelExistenceChecker,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $event = new ControllerEvent(
            $kernel->getWrappedObject(),
            function () {},
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
        );

        $request->attributes = new ParameterBag([]);

        $channelExistenceChecker->withCode(Argument::any())->shouldNotBeCalled();

        $this->checkChannelCode($event);
    }
}
