<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Http;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestBasedLocaleContextSpec extends ObjectBehavior 
{
    public function let(RequestStack $requestStack, LocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($requestStack, $localeProvider);
    }

    public function it_implements_the_locale_context_interface(): void
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    public function it_throws_an_error_if_the_locale_is_not_set_on_request(
        RequestStack $requestStack,
        Request $request
    ): void {
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->get('locale', null)->willReturn(null);
        $request->headers = new ParameterBag([]);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    public function it_throws_an_error_if_the_locale_is_not_available(
        RequestStack $requestStack,
        Request $request,
        LocaleProviderInterface $localeProvider
    ): void {
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->get('locale', null)->willReturn('el');

        $localeProvider->getAvailableLocalesCodes()->willReturn(['en_US', 'de_DE']);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    public function it_returns_the_locale_from_the_request_arguments(
        RequestStack $requestStack,
        Request $request,
        LocaleProviderInterface $localeProvider
    ): void {
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->get('locale', null)->willReturn('en_US');

        $localeProvider->getAvailableLocalesCodes()->willReturn(['en_US', 'de_DE']);

        $this->getLocaleCode()->shouldReturn('en_US');
    }

    public function it_returns_the_locale_from_the_request_headers(
        RequestStack $requestStack,
        Request $request,
        LocaleProviderInterface $localeProvider
    ): void {
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->get('locale', null)->willReturn(null);
        $request->headers = new ParameterBag(['accept-Language' => 'de_DE']);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['en_US', 'de_DE']);

        $this->getLocaleCode()->shouldReturn('de_DE');
    }
}