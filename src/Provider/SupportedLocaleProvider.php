<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Provider;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

final class SupportedLocaleProvider implements SupportedLocaleProviderInterface
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

    private function assertLocaleSupport(string $localeCode, Collection $supportedLocales): void
    {
        $supportedLocaleCodes = [];

        /** @var LocaleInterface $locale */
        foreach ($supportedLocales as $locale) {
            $supportedLocaleCodes[] = $locale->getCode();
        }

        Assert::oneOf($localeCode, $supportedLocaleCodes);
    }
}
