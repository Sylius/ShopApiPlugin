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
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Http\RequestBasedLocaleProviderInterface;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;

final class RequestBasedLocaleProviderSpec extends ObjectBehavior
{
    function let(
        ChannelContextInterface $channelContext,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
    ): void {
        $this->beConstructedWith($channelContext, $supportedLocaleProvider);
    }

    function it_implements_request_based_locale_provider_interface(): void
    {
        $this->shouldImplement(RequestBasedLocaleProviderInterface::class);
    }

    function it_provides_a_locale_code(
        ChannelContextInterface $channelContext,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ChannelInterface $channel,
        Request $request,
    ): void {
        $request->getLocale()->willReturn('fr_FR');

        $channelContext->getChannel()->willReturn($channel);
        $supportedLocaleProvider->provide('fr_FR', $channel)->willReturn('fr_FR');

        $this->getLocaleCode($request)->shouldReturn('fr_FR');
    }

    function it_throws_an_exception_if_channel_cannot_be_resolved(
        ChannelContextInterface $channelContext,
        Request $request,
    ): void {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this
            ->shouldThrow(ChannelNotFoundException::class)
            ->during('getLocaleCode', [$request])
        ;
    }
}
