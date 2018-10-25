<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProvider;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;

final class SupportedLocaleProviderSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(SupportedLocaleProvider::class);
    }

    function it_is_a_supported_locale_provider(): void
    {
        $this->shouldImplement(SupportedLocaleProviderInterface::class);
    }

    function it_fails_to_provide_a_locale_if_no_one_is_given_and_the_channel_has_no_default_locale(
        ChannelInterface $channel
    ): void {
        $channel->getDefaultLocale()->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('provide', [null, $channel]);
    }

    function it_fails_if_the_default_locale_of_the_channel_is_not_supported(
        ChannelInterface $channel,
        LocaleInterface $locale,
        LocaleInterface $supportedLocale
    ): void {
        $channel->getDefaultLocale()->willReturn($locale);
        $locale->getCode()->willReturn('de_DE');

        $channel->getLocales()->willReturn(new ArrayCollection([$supportedLocale->getWrappedObject()]));
        $supportedLocale->getCode()->willReturn('en_US');

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('provide', [null, $channel]);
    }

    function it_fails_if_the_given_locale_is_not_supported(
        ChannelInterface $channel,
        LocaleInterface $supportedLocale
    ): void {
        $channel->getDefaultLocale()->shouldNotBeCalled();

        $channel->getLocales()->willReturn(new ArrayCollection([$supportedLocale->getWrappedObject()]));
        $supportedLocale->getCode()->willReturn('de_DE');

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('provide', ['en_US', $channel]);
    }

    function it_provides_the_locale_if_is_supported(
        ChannelInterface $channel,
        LocaleInterface $supportedLocale
    ): void {
        $channel->getDefaultLocale()->shouldNotBeCalled();

        $channel->getLocales()->willReturn(new ArrayCollection([$supportedLocale->getWrappedObject()]));
        $supportedLocale->getCode()->willReturn('en_US');

        $this->provide('en_US', $channel)->shouldReturn('en_US');
    }

    function it_provides_the_locale_if_it_is_the_channels_default(
        ChannelInterface $channel,
        LocaleInterface $defaultLocale,
        LocaleInterface $supportedLocale1,
        LocaleInterface $supportedLocale2
    ): void {
        $channel->getDefaultLocale()->willReturn($defaultLocale);
        $defaultLocale->getCode()->willReturn('en_US');

        $supportedLocales = [$supportedLocale1->getWrappedObject(), $supportedLocale2->getWrappedObject()];
        $channel->getLocales()->willReturn(new ArrayCollection($supportedLocales));
        $supportedLocale1->getCode()->willReturn('en_US');
        $supportedLocale2->getCode()->willReturn('fr_FR');

        $this->provide(null, $channel)->shouldReturn('en_US');
    }
}
