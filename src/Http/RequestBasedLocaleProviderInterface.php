<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Http;

use Sylius\ShopApiPlugin\Exception\ChannelNotFoundException;
use Symfony\Component\HttpFoundation\Request;

interface RequestBasedLocaleProviderInterface
{
    /** @throws ChannelNotFoundException|\InvalidArgumentException */
    public function getLocaleCode(Request $request): string;
}
