<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

interface LocaleAwareCommandInterface
{
    public function setLocaleCode(string $localeCode);

    public function getLocaleCode(): ?string;
}
