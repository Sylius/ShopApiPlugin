<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Provider;

use Sylius\Component\Core\Model\ChannelInterface;

interface SupportedLocaleProviderInterface
{
    public function provide(?string $localeCode, ChannelInterface $channel): string;
}
