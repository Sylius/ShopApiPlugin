<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\EventListener;

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

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $request->setDefaultLocale($this->localeProvider->getDefaultLocaleCode());

        $localeCode = $request->get('locale');
        if (null === $localeCode) {
            return;
        }

        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();
        if (in_array($localeCode, $availableLocalesCodes, true)) {
            $request->setLocale($localeCode);
        }
    }
}
