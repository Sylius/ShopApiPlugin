<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Provider;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

class SupportedLocaleProvider implements SupportedLocaleProviderInterface
{
    public function provide(?string $localeCode, ChannelInterface $channel): string
    {
        if ($localeCode === null) {
            $defaultLocale = $channel->getDefaultLocale();
            Assert::notNull($defaultLocale);

            $localeCode = $defaultLocale->getCode();
            Assert::notNull($localeCode);
        }

        $this->assertLocaleSupport($localeCode, $channel->getLocales());

        return $localeCode;
    }

    private function assertLocaleSupport(string $localeCode, Collection $supportedLocales)
    {
        $supportedLocaleCodes = [];
        foreach ($supportedLocales as $locale) {
            /** @var LocaleInterface */
            $supportedLocaleCodes[] = $locale->getCode();
        }

        Assert::oneOf($localeCode, $supportedLocaleCodes);
    }
}
