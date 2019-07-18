<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Http;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Exception\ChannelNotFoundException;
use Sylius\ShopApiPlugin\Http\RequestBasedLocaleProviderInterface;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class RequestBasedLocaleProviderSpec extends ObjectBehavior
{
    function let(
        RequestResolverInterface $hostnameBasedRequestResolver,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ): void {
        $this->beConstructedWith($hostnameBasedRequestResolver, $supportedLocaleProvider);
    }

    function it_implements_request_based_locale_provider_interface(): void
    {
        $this->shouldImplement(RequestBasedLocaleProviderInterface::class);
    }

    function it_provides_a_locale_code(
        RequestResolverInterface $hostnameBasedRequestResolver,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ChannelInterface $channel,
        Request $request
    ): void {
        $request->query = new ParameterBag(['locale' => 'fr_FR']);

        $hostnameBasedRequestResolver->findChannel($request)->willReturn($channel);
        $supportedLocaleProvider->provide('fr_FR', $channel)->willReturn('fr_FR');

        $this->getLocaleCode($request)->shouldReturn('fr_FR');
    }

    function it_provides_a_locale_code_even_if_it_is_not_defined_explicitly(
        RequestResolverInterface $hostnameBasedRequestResolver,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ChannelInterface $channel,
        Request $request
    ): void {
        $request->query = new ParameterBag([]);

        $hostnameBasedRequestResolver->findChannel($request)->willReturn($channel);
        $supportedLocaleProvider->provide(null, $channel)->willReturn('en_US');

        $this->getLocaleCode($request)->shouldReturn('en_US');
    }

    function it_throws_an_exception_if_channel_cannot_be_resolved(
        RequestResolverInterface $hostnameBasedRequestResolver,
        Request $request
    ): void {
        $hostnameBasedRequestResolver->findChannel($request)->willReturn(null);

        $this
            ->shouldThrow(ChannelNotFoundException::class)
            ->during('getLocaleCode', [$request])
        ;
    }
}
