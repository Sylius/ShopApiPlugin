<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Http;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class RequestBasedLocaleContext implements LocaleContextInterface
{
    /** @var LocaleProviderInterface */
    private $localeProvider;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack, LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
        $this->requestStack = $requestStack;
    }

    public function getLocaleCode(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        Assert::notNull($request);

        $localeCode = $request->get('locale', null) ?? $request->headers->get('accept-Language', null);
        if (null === $localeCode) {
            throw new LocaleNotFoundException('No locale header set on the current request.');
        }

        // todo: move into sylius core to remove duplicate logic with RequestBasedLocaleProviderContext
        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();
        if (!in_array($localeCode, $availableLocalesCodes, true)) {
            throw LocaleNotFoundException::notAvailable($localeCode, $availableLocalesCodes);
        }

        return $localeCode;
    }
}
