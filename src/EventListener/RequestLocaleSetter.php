<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventListener;

use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class RequestLocaleSetter
{
    /** @var LocaleProviderInterface */
    private $localeProvider;

    public function __construct(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();

        $request->setDefaultLocale($this->localeProvider->getDefaultLocaleCode());

        $localeCode = $request->get('locale');
        if (null === $localeCode && $request->headers->has('Accept-Language')) {
            $localeCode = $request->headers->get('Accept-Language');
        }
        if (null === $localeCode) {
            return;
        }

        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();
        if (in_array($localeCode, $availableLocalesCodes, true)) {
            $request->setLocale($localeCode);
        }
    }
}
