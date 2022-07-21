<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Http;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

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
        if ($request === null) {
            throw new LocaleNotFoundException('The current context has no request.');
        }

        $localeCode = $request->get('locale') ?? $request->headers->get('accept-Language');
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
