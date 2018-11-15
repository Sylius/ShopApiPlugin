<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Http;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Checker\ChannelExistenceCheckerInterface;
use Sylius\ShopApiPlugin\Exception\ChannelNotFoundException;
use Sylius\ShopApiPlugin\Http\RequestBasedLocaleProviderInterface;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class RequestBasedLocaleProviderSpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository,
        ChannelExistenceCheckerInterface $channelExistenceChecker,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ): void {
        $this->beConstructedWith($channelRepository, $channelExistenceChecker, $supportedLocaleProvider);
    }

    function it_implements_request_based_locale_provider_interface(): void
    {
        $this->shouldImplement(RequestBasedLocaleProviderInterface::class);
    }

    function it_uses_channel_code_and_locale_provider_to_return_request_locale(
        ChannelRepositoryInterface $channelRepository,
        ChannelExistenceCheckerInterface $channelExistenceChecker,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ChannelInterface $channel,
        Request $request
    ): void {
        $request->attributes = new ParameterBag(['channelCode' => 'WEB_US']);
        $request->query = new ParameterBag(['locale' => 'fr_FR']);

        $channelExistenceChecker->withCode('WEB_US')->shouldBeCalled();
        $channelRepository->findOneByCode('WEB_US')->willReturn($channel);

        $supportedLocaleProvider->provide('fr_FR', $channel)->willReturn('fr_FR');

        $this->getLocaleCode($request)->shouldReturn('fr_FR');
    }

    function it_provides_locale_code_even_if_it_is_not_defined_explicitly(
        ChannelRepositoryInterface $channelRepository,
        ChannelExistenceCheckerInterface $channelExistenceChecker,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ChannelInterface $channel,
        Request $request
    ): void {
        $request->attributes = new ParameterBag(['channelCode' => 'WEB_US']);
        $request->query = new ParameterBag([]);

        $channelExistenceChecker->withCode('WEB_US')->shouldBeCalled();
        $channelRepository->findOneByCode('WEB_US')->willReturn($channel);

        $supportedLocaleProvider->provide(null, $channel)->willReturn('en_US');

        $this->getLocaleCode($request)->shouldReturn('en_US');
    }

    function it_throws_exception_if_channel_code_is_not_defined(Request $request): void
    {
        $request->attributes = new ParameterBag([]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getLocaleCode', [$request])
        ;
    }

    function it_throws_exception_if_channel_with_given_code_does_not_exist(
        Request $request,
        ChannelExistenceCheckerInterface $channelExistenceChecker
    ): void {
        $request->attributes = new ParameterBag(['channelCode' => 'WEB_PL']);

        $channelExistenceChecker->withCode('WEB_PL')->willThrow(ChannelNotFoundException::class);

        $this
            ->shouldThrow(ChannelNotFoundException::class)
            ->during('getLocaleCode', [$request])
        ;
    }
}
